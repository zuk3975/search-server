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

namespace Apisearch\Plugin\Elastica\Domain\LogRepository;

use Apisearch\Log\Log;
use Apisearch\Plugin\Elastica\Domain\Builder\TimeFormatBuilder;
use Apisearch\Plugin\Elastica\Domain\ElasticaWrapper;
use Apisearch\Plugin\Elastica\Domain\ElasticaWrapperWithRepositoryReference;
use Apisearch\Server\Domain\Repository\LogRepository\IndexRepository as IndexRepositoryInterface;
use Elastica\Document;
use Elastica\Document as ElasticaDocument;

/**
 * Class IndexRepository.
 */
class IndexRepository extends ElasticaWrapperWithRepositoryReference implements IndexRepositoryInterface
{
    /**
     * @var TimeFormatBuilder
     *
     * Time format builder
     */
    private $timeFormatBuilder;

    /**
     * ElasticaSearchRepository constructor.
     *
     * @param ElasticaWrapper   $elasticaWrapper
     * @param string            $repositoryConfigPath
     * @param TimeFormatBuilder $timeFormatBuilder
     */
    public function __construct(
        ElasticaWrapper $elasticaWrapper,
        string $repositoryConfigPath,
        TimeFormatBuilder $timeFormatBuilder
    ) {
        parent::__construct(
            $elasticaWrapper,
            $repositoryConfigPath
        );

        $this->timeFormatBuilder = $timeFormatBuilder;
    }

    /**
     * Generate log document.
     *
     * @param Log $log
     */
    public function addLog(Log $log)
    {
        $this
            ->elasticaWrapper
            ->addDocuments(
                $this->normalizeRepositoryReferenceCrossIndices(
                    $this->getRepositoryReference()
                ),
                [$this->createLogDocument($log)]
            );

        $this->refresh();
    }

    /**
     * Create item document.
     *
     * @param Log $log
     *
     * @return Document
     */
    private function createLogDocument(Log $log): Document
    {
        $formattedTime = $this
            ->timeFormatBuilder
            ->formatTimeFromMillisecondsToBasicDateTime(
                $log->getOccurredOn()
            );

        $itemDocument = [
            'uuid' => [
                'id' => $log->getId(),
                'type' => $log->getType(),
            ],
            'payload' => $log->getPayload(),
            'indexed_metadata' => [
                'occurred_on' => $formattedTime,
            ],
        ];

        return new ElasticaDocument(
            $log->getId(),
            $itemDocument
        );
    }
}
