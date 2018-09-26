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

namespace Apisearch\Plugin\RedisMetadataFields\Domain\Repository;

use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Plugin\Redis\Domain\RedisWrapper;
use Apisearch\Repository\RepositoryReference;

/**
 * Class RedisMetadataRepository.
 */
class RedisMetadataRepository
{
    /**
     * @var RedisWrapper
     *
     * RedisWrapper
     */
    private $redisWrapper;

    /**
     * @var string
     *
     * Key
     */
    private $key;

    /**
     * RedisMetadataRepository constructor.
     *
     * @param RedisWrapper $redisWrapper
     * @param string       $key
     */
    public function __construct(
        RedisWrapper $redisWrapper,
        string $key
    ) {
        $this->redisWrapper = $redisWrapper;
        $this->key = $key;
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
                    $this->key,
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
                    $this->key,
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
                    $this->key,
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
