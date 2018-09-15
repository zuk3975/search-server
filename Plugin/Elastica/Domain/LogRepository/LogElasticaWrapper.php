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

use Apisearch\Config\Config;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Plugin\Elastica\Domain\ElasticaWrapper;
use Apisearch\Repository\RepositoryReference;
use Elastica\Type\Mapping;

/**
 * Class LogElasticaWrapper.
 */
class LogElasticaWrapper extends ElasticaWrapper
{
    /**
     * @var string
     *
     * Item type
     */
    const ITEM_TYPE = 'log_entry';

    /**
     * Get item type.
     *
     * @return string
     */
    public function getItemType(): string
    {
        return self::ITEM_TYPE;
    }

    public function getIndexPrefix(): string
    {
        return 'apisearch_log';
    }

    /**
     * Get index name.
     *
     * @param RepositoryReference $repositoryReference
     *
     * @return string
     */
    public function getIndexName(RepositoryReference $repositoryReference): string
    {
        return $this->buildIndexReference(
            $repositoryReference,
            $this->getIndexPrefix()
        );
    }

    /**
     * Get index not available exception.
     *
     * @param string $message
     *
     * @return ResourceNotAvailableException
     */
    public function getIndexNotAvailableException(string $message): ResourceNotAvailableException
    {
        return ResourceNotAvailableException::logsIndexNotAvailable($message);
    }

    /**
     * Get immutable index configuration.
     *
     * @param Config $config
     * @param int    $shards
     * @param int    $replicas
     *
     * @return array
     */
    public function getImmutableIndexConfiguration(
        Config $config,
        int $shards,
        int $replicas
    ): array {
        return [
            'number_of_shards' => $shards,
            'number_of_replicas' => $replicas,
        ];
    }

    /**
     * Get index configuration.
     *
     * @param Config $config
     * @param int    $shards
     * @param int    $replicas
     *
     * @return array
     */
    public function getIndexConfiguration(
        Config $config,
        int $shards,
        int $replicas
    ): array {
        return [];
    }

    /**
     * Build index mapping.
     *
     * @param Mapping $mapping
     * @param Config  $config
     */
    public function buildIndexMapping(
        Mapping $mapping,
        Config $config
    ) {
        $mapping->setProperties([
            'uuid' => [
                'type' => 'object',
                'dynamic' => 'strict',
                'properties' => [
                    'id' => [
                        'type' => 'keyword',
                    ],
                    'type' => [
                        'type' => 'keyword',
                    ],
                ],
            ],
            'indexed_metadata' => [
                'type' => 'object',
                'dynamic' => false,
                'properties' => [
                    'occurred_on' => [
                        'type' => 'date',
                        'format' => 'basic_date_time',
                    ],
                ],
            ],
            'payload' => [
                'type' => 'text',
                'index' => false,
            ],
        ]);
    }
}
