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

namespace Apisearch\Server\Domain\CommandHandler;

use Apisearch\Server\Domain\Command\UpdateItems;
use Apisearch\Server\Domain\Event\ItemsWereUpdated;
use Apisearch\Server\Domain\WithRepositoryAndEventPublisher;

/**
 * Class UpdateItemsHandler.
 */
class UpdateItemsHandler extends WithRepositoryAndEventPublisher
{
    /**
     * Update items.
     *
     * @param UpdateItems $updateItems
     */
    public function handle(UpdateItems $updateItems)
    {
        $query = $updateItems->getQuery();
        $changes = $updateItems->getChanges();

        $this
            ->repository
            ->setRepositoryReference($updateItems->getRepositoryReference());

        $this
            ->repository
            ->updateItems(
                $query,
                $changes
            );

        $this
            ->eventPublisher
            ->publish(new ItemsWereUpdated(
                $query->getFilters(),
                $changes
            ));
    }
}
