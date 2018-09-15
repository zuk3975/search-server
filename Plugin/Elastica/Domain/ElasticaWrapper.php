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

namespace Apisearch\Plugin\Elastica\Domain;

use Apisearch\Config\Config;
use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Model\AppUUID;
use Apisearch\Model\Index as ApisearchIndex;
use Apisearch\Model\IndexUUID;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Exception\ParsedCreatingIndexException;
use Elastica\Client;
use Elastica\Document;
use Elastica\Exception\Bulk\ResponseException as BulkResponseException;
use Elastica\Exception\ResponseException;
use Elastica\Index;
use Elastica\Query;
use Elastica\Type;
use Elastica\Type\Mapping;
use Elasticsearch\Endpoints\Cat\Indices;

/**
 * Class ElasticaWrapper.
 */
abstract class ElasticaWrapper
{
    /**
     * @var Client
     *
     * Elastica client
     */
    private $client;

    /**
     * Construct.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get item type.
     *
     * @return string
     */
    abstract public function getItemType(): string;

    /**
     * Get index prefix.
     *
     * @return string
     */
    abstract public function getIndexPrefix(): string;

    /**
     * Get index name.
     *
     * @param RepositoryReference $repositoryReference
     *
     * @return string
     */
    abstract public function getIndexName(RepositoryReference $repositoryReference): string;

    /**
     * Get index not available exception.
     *
     * @param string $message
     *
     * @return ResourceNotAvailableException
     */
    abstract public function getIndexNotAvailableException(string $message): ResourceNotAvailableException;

    /**
     * Get immutable index configuration.
     *
     * @param Config $config
     * @param int    $shards
     * @param int    $replicas
     *
     * @return array
     */
    abstract public function getImmutableIndexConfiguration(
        Config $config,
        int $shards,
        int $replicas
    ): array;

    /**
     * Get index configuration.
     *
     * @param Config $config
     * @param int    $shards
     * @param int    $replicas
     *
     * @return array
     */
    abstract public function getIndexConfiguration(
        Config $config,
        int $shards,
        int $replicas
    ): array;

    /**
     * Build index mapping.
     *
     * @param Mapping $mapping
     * @param Config  $config
     */
    abstract public function buildIndexMapping(
        Mapping $mapping,
        Config $config
    );

    /**
     * Get search index.
     *
     * @param RepositoryReference $repositoryReference
     *
     * @return Index
     */
    public function getIndex(RepositoryReference $repositoryReference): Index
    {
        return $this
            ->client
            ->getIndex($this->getIndexName($repositoryReference));
    }

    /**
     * Get indices.
     *
     * @param RepositoryReference $repositoryReference
     *
     * @return ApisearchIndex[]
     */
    public function getIndices(RepositoryReference $repositoryReference): array
    {
        $appUUIDComposed = $repositoryReference->getAppUUID()->composeUUID();

        $indexSearchKeyword = $this->getIndexPrefix().
            (!empty($appUUIDComposed) ? '_'.$appUUIDComposed : '').
            '*';

        $elasticaResponse = $this->client->requestEndpoint((new Indices())->setIndex($indexSearchKeyword));

        if (empty($elasticaResponse->getData())) {
            return [];
        }

        $regexToParse = '/^'.
            '(?P<color>[^\ ]+)\s+'.
            '(?P<status>[^\ ]+)\s+'.
            ''.$this->getIndexPrefix().'\_(?P<app_id>[^\_]+)\_(?P<id>[^\ ]+)\s+'.
            '(?P<uuid>[^\ ]+)\s+'.
            '(?P<primary_shards>[^\ ]+)\s+'.
            '(?P<replica_shards>[^\ ]+)\s+'.
            '(?P<doc_count>[^\ ]+)\s+'.
            '(?P<doc_deleted>[^\ ]+)\s+'.
            '(?P<index_size>[^\ ]+)\s+'.
            '(?P<storage_size>[^\ ]+)'.
            '$/im';

        $indices = [];
        preg_match_all($regexToParse, $elasticaResponse->getData()['message'], $matches, PREG_SET_ORDER, 0);
        if ($matches) {
            foreach ($matches as $metaData) {
                $indices[] = new ApisearchIndex(
                    IndexUUID::createById($metaData['id']),
                    AppUUID::createById($metaData['app_id']),
                    (
                        'open' === $metaData['status'] &&
                        in_array($metaData['color'], ['green', 'yellow'])
                    ),
                    (int) $metaData['doc_count'],
                    (string) $metaData['index_size']
                );
            }
        }

        return $indices;
    }

    /**
     * Get index stats.
     *
     * @param RepositoryReference $repositoryReference
     *
     * @return Index\Stats
     */
    public function getIndexStats(RepositoryReference $repositoryReference): Index\Stats
    {
        try {
            return $this
                ->client
                ->getIndex($this->getIndexName($repositoryReference))
                ->getStats();
        } catch (ResponseException $exception) {
            /*
             * The index resource cannot be deleted.
             * This means that the resource is not available
             */
            throw $this->getIndexNotAvailableException($exception->getMessage());
        }
    }

    /**
     * Delete index.
     *
     * @param RepositoryReference $repositoryReference
     *
     * @throws ResourceNotAvailableException
     */
    public function deleteIndex(RepositoryReference $repositoryReference)
    {
        try {
            $searchIndex = $this->getIndex($repositoryReference);
            $searchIndex->clearCache();
            $searchIndex->delete();
        } catch (ResponseException $exception) {
            /*
             * The index resource cannot be deleted.
             * This means that the resource is not available
             */
            throw $this->getIndexNotAvailableException($exception->getMessage());
        }
    }

    /**
     * Remove index.
     *
     * @param RepositoryReference $repositoryReference
     *
     * @throws ResourceNotAvailableException
     */
    public function resetIndex(RepositoryReference $repositoryReference)
    {
        try {
            $searchIndex = $this->getIndex($repositoryReference);
            $searchIndex->clearCache();
            $searchIndex->deleteByQuery(new Query\MatchAll());
        } catch (ResponseException $exception) {
            /*
             * The index resource cannot be deleted.
             * This means that the resource is not available
             */
            throw $this->getIndexNotAvailableException($exception->getMessage());
        }
    }

    /**
     * Create index.
     *
     * @param RepositoryReference $repositoryReference
     * @param Config              $config
     * @param int                 $shards
     * @param int                 $replicas
     *
     * @throws ResourceExistsException
     */
    public function createIndex(
        RepositoryReference $repositoryReference,
        Config $config,
        int $shards,
        int $replicas
    ) {
        $searchIndex = $this->getIndex($repositoryReference);

        try {
            $searchIndex->create($this->getImmutableIndexConfiguration(
                $config,
                $shards,
                $replicas
            ));
        } catch (ResponseException $exception) {
            throw ParsedCreatingIndexException::parse($exception->getMessage());
        }
    }

    /**
     * Configure index.
     *
     * @param RepositoryReference $repositoryReference
     * @param Config              $config
     * @param int                 $shards
     * @param int                 $replicas
     *
     * @throws ResourceExistsException
     */
    public function configureIndex(
        RepositoryReference $repositoryReference,
        Config $config,
        int $shards,
        int $replicas
    ) {
        $searchIndex = $this->getIndex($repositoryReference);
        $indexConfigAsArray = $this->getIndexConfiguration(
            $config,
            $shards,
            $replicas
        );
        unset($indexConfigAsArray['number_of_shards']);
        unset($indexConfigAsArray['number_of_replicas']);
        $searchIndex->close();
        $searchIndex->setSettings($indexConfigAsArray);
        $searchIndex->open();
    }

    /**
     * Create index.
     *
     * @param RepositoryReference $repositoryReference
     * @param string              $typeName
     *
     * @return Type
     */
    public function getType(
        RepositoryReference $repositoryReference,
        string $typeName
    ) {
        return $this
            ->getIndex($repositoryReference)
            ->getType($typeName);
    }

    /**
     * Search.
     *
     * @param RepositoryReference $repositoryReference
     * @param Query               $query
     * @param int                 $from
     * @param int                 $size
     *
     * @return array
     */
    public function search(
        RepositoryReference $repositoryReference,
        Query $query,
        int $from,
        int $size
    ): array {
        try {
            $queryResult = $this
                ->getIndex($repositoryReference)
                ->search($query, [
                    'from' => $from,
                    'size' => $size,
                ]);
        } catch (ResponseException $exception) {
            /*
             * The index resource cannot be deleted.
             * This means that the resource is not available
             */

            throw $this->getIndexNotAvailableException($exception->getMessage());
        }

        return [
            'results' => $queryResult->getResults(),
            'suggests' => $queryResult->getSuggests(),
            'aggregations' => $queryResult->getAggregations(),
            'total_hits' => $queryResult->getTotalHits(),
        ];
    }

    /**
     * Refresh.
     *
     * @param RepositoryReference $repositoryReference
     */
    public function refresh(RepositoryReference $repositoryReference)
    {
        $this
            ->getIndex($repositoryReference)
            ->refresh();
    }

    /**
     * Create mapping.
     *
     * @param RepositoryReference $repositoryReference
     * @param Config              $config
     *
     * @throws ResourceExistsException
     */
    public function createIndexMapping(
        RepositoryReference $repositoryReference,
        Config $config
    ) {
        try {
            $itemMapping = new Mapping();
            $itemMapping->setType($this->getType($repositoryReference, $this->getItemType()));
            $this->buildIndexMapping($itemMapping, $config);
            $itemMapping->send();
        } catch (ResponseException $exception) {
            /*
             * The index resource cannot be deleted.
             * This means that the resource is not available
             */
            throw $this->getIndexNotAvailableException($exception->getMessage());
        }
    }

    /**
     * Add documents.
     *
     * @param RepositoryReference $repositoryReference
     * @param Document[]          $documents
     *
     * @throws ResourceExistsException
     */
    public function addDocuments(
        RepositoryReference $repositoryReference,
        array $documents
    ) {
        try {
            $this
                ->getType($repositoryReference, $this->getItemType())
                ->addDocuments($documents);
        } catch (BulkResponseException $exception) {
            /*
             * The index resource cannot be deleted.
             * This means that the resource is not available
             */
            throw $this->getIndexNotAvailableException($exception->getMessage());
        }
    }

    /**
     * Delete documents by its.
     *
     * @param RepositoryReference $repositoryReference
     * @param string[]            $documentsId
     *
     * @throws ResourceExistsException
     */
    public function deleteDocumentsByIds(
        RepositoryReference $repositoryReference,
        array $documentsId
    ) {
        try {
            $this
                ->getType($repositoryReference, $this->getItemType())
                ->deleteByQuery(new Query\Ids(array_values($documentsId)));
        } catch (ResponseException $exception) {
            /*
             * The index resource cannot be deleted.
             * This means that the resource is not available
             */
            throw $this->getIndexNotAvailableException($exception->getMessage());
        }
    }

    /**
     * Build specific index reference.
     *
     * @param RepositoryReference $repositoryReference
     * @param string              $prefix
     *
     * @return string
     */
    protected function buildIndexReference(
        RepositoryReference $repositoryReference,
        string $prefix
    ) {
        if (is_null($repositoryReference->getAppUUID())) {
            return '';
        }

        $appId = $repositoryReference->getAppUUID()->composeUUID();
        if (is_null($repositoryReference->getIndexUUID())) {
            return "{$prefix}_{$appId}";
        }

        $indexId = $repositoryReference->getIndexUUID()->composeUUID();
        if ('*' === $indexId) {
            return "{$prefix}_{$appId}_*";
        }

        $splittedIndexId = explode(',', $indexId);

        return implode(',', array_map(function (string $indexId) use ($prefix, $appId) {
            return trim("{$prefix}_{$appId}_$indexId", '_ ');
        }, $splittedIndexId));
    }
}
