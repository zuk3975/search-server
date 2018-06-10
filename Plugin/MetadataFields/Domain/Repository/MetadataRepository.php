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
 * Interface MetadataRepository.
 */
interface MetadataRepository
{
    /**
     * Save Item metadata to storage.
     *
     * @param RepositoryReference $repositoryReference
     * @param Item[]              $items
     */
    public function saveItemsMetadata(
        RepositoryReference $repositoryReference,
        array $items
    );

    /**
     * Load Items metadata with locally saved data.
     *
     * @param RepositoryReference $repositoryReference
     * @param Item[]              $items
     */
    public function loadItemsMetadata(
        RepositoryReference $repositoryReference,
        array $items
    );

    /**
     * Delete Items metadata.
     *
     * @param RepositoryReference $repositoryReference
     * @param ItemUUID[]          $itemsUUID
     */
    public function deleteItemsMetadata(
        RepositoryReference $repositoryReference,
        array $itemsUUID
    );
}
