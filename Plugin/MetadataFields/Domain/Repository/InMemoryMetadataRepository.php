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

namespace Apisearch\Plugin\MetadataFields\Domain\Repository;

use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Repository\RepositoryReference;

/**
 * Class InMemoryMetadataRepository.
 */
class InMemoryMetadataRepository implements MetadataRepository
{
    /**
     * @var array
     *
     * Storage
     */
    private $storage = [];

    /**
     * Get Storage.
     *
     * @return array
     */
    public function getStorage(): array
    {
        return $this->storage;
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
            $this->storage[$this->composeKey($repositoryReference, $item->getUUID())] = $item->getMetadata();
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
            $item->setMetadata($this->storage[$this->composeKey($repositoryReference, $item->getUUID())] ?? []);
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
            unset($this->storage[$this->composeKey($repositoryReference, $itemUUID)]);
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
