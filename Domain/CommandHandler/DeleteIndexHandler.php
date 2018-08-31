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

namespace Apisearch\Server\Domain\CommandHandler;

use Apisearch\Server\Domain\Command\DeleteIndex;
use Apisearch\Server\Domain\WithAppRepositoryAndEventPublisher;

/**
 * Class DeleteIndexHandler.
 */
class DeleteIndexHandler extends WithAppRepositoryAndEventPublisher
{
    /**
     * Delete the index.
     *
     * @param DeleteIndex $deleteIndex
     */
    public function handle(DeleteIndex $deleteIndex)
    {
        $repositoryReference = $deleteIndex->getRepositoryReference();
        $indexUUID = $deleteIndex->getIndexUUID();

        $this
            ->appRepository
            ->setRepositoryReference($repositoryReference);

        $this
            ->appRepository
            ->deleteIndex($indexUUID);
    }
}
