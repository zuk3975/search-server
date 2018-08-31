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

use Apisearch\Server\Domain\Command\CreateIndex;
use Apisearch\Server\Domain\WithAppRepositoryAndEventPublisher;

/**
 * Class CreateIndexHandler.
 */
class CreateIndexHandler extends WithAppRepositoryAndEventPublisher
{
    /**
     * Create the index.
     *
     * @param CreateIndex $createIndex
     */
    public function handle(CreateIndex $createIndex)
    {
        $repositoryReference = $createIndex->getRepositoryReference();
        $indexUUID = $createIndex->getIndexUUID();
        $config = $createIndex->getConfig();

        $this
            ->appRepository
            ->setRepositoryReference($repositoryReference);

        $this
            ->appRepository
            ->createIndex(
                $indexUUID,
                $config
            );
    }
}
