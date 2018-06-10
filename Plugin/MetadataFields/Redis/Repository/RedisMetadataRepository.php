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
 * @author PuntMig Technologies
 */

declare(strict_types=1);

namespace Apisearch\Plugin\MetadataFields\Redis\Repository;

use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Plugin\MetadataFields\Domain\Repository\MetadataRepository;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Redis\RedisWrapper;

/**
 * Class RedisMetadataRepository.
 */
class RedisMetadataRepository implements MetadataRepository
{
    /**
     * @var string
     *
     * Plugin key
     */
    const PLUGIN_KEY = 'apisearch_server.redis_wrapper';

    /**
     * @var RedisWrapper
     *
     * RedisWrapper
     */
    private $redisWrapper;

    /**
     * RedisMetadataRepository constructor.
     *
     * @param RedisWrapper $redisWrapper
     */
    public function __construct(RedisWrapper $redisWrapper)
    {
        $this->redisWrapper = $redisWrapper;
    }

    /**
     * Save Item metadata to storage.
     *
     * @param RepositoryReference $repositoryReference
     * @param Item[]              $items
     */
    public function saveItemsMetadata(
        RepositoryReference $repositoryReference,
        array $items
    ) {
        array_walk($items, function (Item $item) use ($repositoryReference) {
            $this
                ->redisWrapper
                ->getClient()
                ->hSet(
                    self::PLUGIN_KEY,
                    $this->composeKey($repositoryReference, $item->getUUID()),
                    json_encode($item->getMetadata())
                );

            $item->setMetadata([]);
        });
    }

    /**
     * Load Items metadata with locally saved data.
     *
     * @param RepositoryReference $repositoryReference
     * @param Item[]              $items
     */
    public function loadItemsMetadata(
        RepositoryReference $repositoryReference,
        array $items
    ) {
        array_walk($items, function (Item $item) use ($repositoryReference) {
            $metadata = $this
                ->redisWrapper
                ->getClient()
                ->hGet(
                    self::PLUGIN_KEY,
                    $this->composeKey($repositoryReference, $item->getUUID())
                );

            $item->setMetadata(
                (false === $metadata)
                    ? []
                    : json_decode($metadata, true)
            );
        });
    }

    /**
     * Delete Items metadata.
     *
     * @param RepositoryReference $repositoryReference
     * @param ItemUUID[]          $itemsUUID
     */
    public function deleteItemsMetadata(
        RepositoryReference $repositoryReference,
        array $itemsUUID
    ) {
        array_walk($itemsUUID, function (ItemUUID $itemUUID) use ($repositoryReference) {
            $this
                ->redisWrapper
                ->getClient()
                ->hDel(
                    self::PLUGIN_KEY,
                    $this->composeKey($repositoryReference, $itemUUID)
                );
        });
    }

    /**
     * Compose item key.
     *
     * @param RepositoryReference $repositoryReference
     * @param ItemUUID            $itemUUID
     *
     * @return string
     */
    public function composeKey(
        RepositoryReference $repositoryReference,
        ItemUUID $itemUUID
    ): string {
        return sprintf('%s~~%s',
            $repositoryReference->compose(),
            $itemUUID->composeUUID()
        );
    }
}
