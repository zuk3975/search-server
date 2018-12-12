<?php

/*
 * This file is part of the Apisearch Server
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Apisearch\Plugin\Elastica\Domain\Repository;

use Apisearch\Config\Campaign;
use Apisearch\Config\Campaigns;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Plugin\Elastica\Domain\Builder\QueryBuilder;
use Apisearch\Plugin\Elastica\Domain\Builder\ResultBuilder;
use Apisearch\Plugin\Elastica\Domain\ElasticaWrapper;
use Apisearch\Plugin\Elastica\Domain\ElasticaWrapperWithRepositoryReference;
use Apisearch\Query\Filter;
use Apisearch\Query\Query;
use Apisearch\Result\Result;
use Apisearch\Server\Domain\Repository\Repository\QueryRepository as QueryRepositoryInterface;
use Carbon\Carbon;
use Elastica\Query as ElasticaQuery;
use Elastica\Suggest;
use Exception;

/**
 * Class QueryRepository.
 */
class QueryRepository extends ElasticaWrapperWithRepositoryReference implements QueryRepositoryInterface
{
    /**
     * @var QueryBuilder
     *
     * Query builder
     */
    private $queryBuilder;

    /**
     * @var ResultBuilder
     *
     * Result builder
     */
    private $resultBuilder;

    /**
     * ElasticaSearchRepository constructor.
     *
     * @param ElasticaWrapper $elasticaWrapper
     * @param string          $repositoryConfigPath
     * @param QueryBuilder    $queryBuilder
     * @param ResultBuilder   $resultBuilder
     */
    public function __construct(
        ElasticaWrapper $elasticaWrapper,
        string $repositoryConfigPath,
        QueryBuilder $queryBuilder,
        ResultBuilder $resultBuilder
    ) {
        parent::__construct(
            $elasticaWrapper,
            $repositoryConfigPath
        );

        $this->queryBuilder = $queryBuilder;
        $this->resultBuilder = $resultBuilder;
    }

    /**
     * Search cross the index types.
     *
     * @param Query $query
     *
     * @return Result
     */
    public function query(Query $query): Result
    {
        $mainQuery = new ElasticaQuery();
        $boolQuery = new ElasticaQuery\BoolQuery();
        $this
            ->queryBuilder
            ->buildQuery(
                $query,
                $mainQuery,
                $boolQuery
            );

        $this->promoteUUIDs(
            $boolQuery,
            $query->getItemsPromoted()
        );

        if ($query->areHighlightEnabled()) {
            $this->addHighlights($mainQuery);
        }

        $this->addCampaigns($query, $boolQuery);

        $this->addSuggest(
            $mainQuery,
            $query
        );

        $results = $this
            ->elasticaWrapper
            ->search(
                $this->getRepositoryReference(),
                $mainQuery,
                $query->areResultsEnabled()
                    ? $query->getFrom()
                    : 0,
                $query->areResultsEnabled()
                    ? $query->getSize()
                    : 0
            );

        $rawQ = $mainQuery->toArray();
        $rawQ['from'] = $query->areResultsEnabled() ? $query->getFrom() : 0;
        $rawQ['sort'] = $query->getSortBy()->toArray()['sortBy'] ?? ['_score' => 'desc'];
        $rawQ['size'] = $query->areResultsEnabled() ? $query->getSize() : 0;
        $query->setRawQuery($rawQ);

        return $this->elasticaResultToResult(
            $query,
            $results
        );
    }

    /**
     * Build a Result object given elastica result object.
     *
     * @param Query $query
     * @param array $elasticaResults
     *
     * @return Result
     */
    private function elasticaResultToResult(
        Query $query,
        array $elasticaResults
    ): Result {
        $resultAggregations = [];

        /*
         * Build Result instance
         */
        if (
            $query->areAggregationsEnabled() &&
            isset($elasticaResults['aggregations']['all'])
        ) {
            $resultAggregations = $elasticaResults['aggregations']['all']['universe'];
            unset($resultAggregations['common']);

            $result = new Result(
                $query,
                $elasticaResults['aggregations']['all']['universe']['doc_count'],
                $elasticaResults['total_hits']
            );
        } else {
            $result = new Result(
                $query,
                0,
                $elasticaResults['total_hits']
            );
        }

        /*
         * @var ElasticaResult
         */
        foreach ($elasticaResults['results'] as $elasticaResult) {
            $source = $elasticaResult->getSource();

            if (
                isset($elasticaResult->getParam('sort')[0]) &&
                is_float($elasticaResult->getParam('sort')[0])
            ) {
                $source['distance'] = $elasticaResult->getParam('sort')[0];
            }

            $item = Item::createFromArray($source);
            $score = $elasticaResult->getScore();
            $item->setScore(is_float($score)
                ? $score
                : 1
            );

            if ($query->areHighlightEnabled()) {
                $formedHighlights = [];
                foreach ($elasticaResult->getHighlights() as $highlightField => $highlightValue) {
                    $formedHighlights[str_replace('searchable_metadata.', '', $highlightField)] = $highlightValue[0];
                }

                $item->setHighlights($formedHighlights);
            }

            $result->addItem($item);
        }

        if (
            $query->areAggregationsEnabled() &&
            isset($resultAggregations['doc_count'])
        ) {
            $result->setAggregations(
                $this
                    ->resultBuilder
                    ->buildResultAggregations(
                        $query,
                        $resultAggregations
                    )
            );
        }

        /*
         * Build suggests
         */
        if (isset($elasticaResults['suggests']['completion']) && $query->areSuggestionsEnabled()) {
            foreach ($elasticaResults['suggests']['completion'][0]['options'] as $suggest) {
                $result->addSuggest($suggest['text']);
            }
        }

        return $result;
    }

    /**
     * Add suggest into an Elastica Query.
     *
     * @param ElasticaQuery $mainQuery
     * @param Query         $query
     */
    private function addSuggest($mainQuery, $query)
    {
        if ($query->areSuggestionsEnabled()) {
            $completitionText = new Suggest\Completion(
                'completion',
                'suggest'
            );
            $completitionText->setText($query->getQueryText());

            $mainQuery->setSuggest(
                new Suggest($completitionText)
            );
        }
    }

    /**
     * Promote UUID.
     *
     * The boosting values go from 1 (not included) to 3 (not included)
     *
     * @param ElasticaQuery\BoolQuery $boolQuery
     * @param ItemUUID[]              $itemsPriorized
     */
    private function promoteUUIDs(
        ElasticaQuery\BoolQuery $boolQuery,
        array $itemsPriorized
    ) {
        if (empty($itemsPriorized)) {
            return;
        }

        $it = count($itemsPriorized);
        foreach ($itemsPriorized as $position => $itemUUID) {
            $boolQuery->addShould(new ElasticaQuery\Term([
                '_id' => [
                    'value' => $itemUUID->composeUUID(),
                    'boost' => 10 + ($it-- / (count($itemsPriorized) + 1)),
                ],
            ]));
        }
    }

    /**
     * Highlight.
     *
     * @param ElasticaQuery $query
     */
    private function addHighlights(ElasticaQuery $query)
    {
        $query->setHighlight([
            'fields' => [
                '*' => [
                    'fragment_size' => 100,
                    'number_of_fragments' => 3,
                ],
            ],
        ]);
    }

    /**
     * Add campaigns.
     *
     * @param Query                   $query
     * @param ElasticaQuery\BoolQuery $elasticaQuery
     */
    private function addCampaigns(
        Query $query,
        ElasticaQuery\BoolQuery $elasticaQuery
    ) {
        $file = $this->getConfigPath($this->getRepositoryReference()).'/campaigns.json';
        if (!is_file($file)) {
            return;
        }

        try {
            $campaignsAsArray = json_decode(file_get_contents($file), true);
        } catch (Exception $e) {
            return;
        }

        $now = Carbon::now('UTC')->timestamp;
        $enabledCampaigns = array_filter(
            Campaigns::createFromArray($campaignsAsArray)->getCampaigns(),
            function (Campaign $campaign) use ($query, $now) {
                return
                    $campaign->isEnabled() &&
                    !empty($campaign->getBoostClauses()) &&
                    (
                        is_null($campaign->getFrom()) ||
                        $campaign->getFrom()->getTimestamp() <= $now
                    ) &&
                    (
                        is_null($campaign->getTo()) ||
                        $campaign->getTo()->getTimestamp() > $now
                    ) &&
                    (
                        empty($campaign->getQueryText()) ||
                        (1 === preg_match('~^'.$campaign->getQueryText().'$~i', $query->getQueryText()))
                    ) &&
                    (
                        empty($campaign->getAppliedFilters()) ||
                        empty(array_filter(
                            $campaign->getAppliedFilters(),
                            function ($values, string $field) use ($query) {
                                $values = is_array($values) ? $values : [$values];
                                $filter = $query->getFilterByField($field);
                                if (is_null($filter)) {
                                    return true;
                                }

                                return !empty(array_intersect($values, $filter->getValues()));
                            },
                            ARRAY_FILTER_USE_BOTH
                        ))
                    )
                ;
            }
        );

        if (empty($enabledCampaigns)) {
            return;
        }

        /**
         * @var Campaign
         */
        $boolQuery = new ElasticaQuery\BoolQuery();
        foreach ($enabledCampaigns as $enabledCampaign) {
            foreach ($enabledCampaign->getBoostClauses() as $boostClause) {
                $boosting = $boostClause->getBoost();
                foreach ($boostClause->getValues() as $value) {
                    $term = new ElasticaQuery\Term([
                        Filter::getFilterPathByField($boostClause->getField()) => [
                            'value' => $value,
                            'boost' => $boosting,
                        ],
                    ]);
                    $boolQuery->addShould($term);
                }
            }
        }

        Campaign::MODE_BOOST === $enabledCampaign->getMode()
            ? $elasticaQuery->addShould($boolQuery)
            : $elasticaQuery->addMust($boolQuery);
    }
}
