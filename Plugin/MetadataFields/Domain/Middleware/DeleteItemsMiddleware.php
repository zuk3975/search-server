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

namespace Apisearch\Plugin\MetadataFields\Domain\Middleware;

use Apisearch\Plugin\MetadataFields\Domain\Repository\MetadataRepository;
use Apisearch\Server\Domain\Command\DeleteItems;
use Apisearch\Server\Domain\CommandWithRepositoryReferenceAndToken;
use Apisearch\Server\Domain\Plugin\PluginMiddleware;

/**
 * Class DeleteItemsMiddleware.
 */
class DeleteItemsMiddleware implements PluginMiddleware
{
    /**
     * @var MetadataRepository
     *
     * Metadata repository
     */
    private $metadataRepository;

    /**
     * OnItemsWereIndexed constructor.
     *
     * @param MetadataRepository $metadataRepository
     */
    public function __construct(MetadataRepository $metadataRepository)
    {
        $this->metadataRepository = $metadataRepository;
    }

    /**
     * Execute middleware.
     *
     * @param CommandWithRepositoryReferenceAndToken $command
     * @param callable                               $next
     *
     * @return mixed
     */
    public function execute(
        CommandWithRepositoryReferenceAndToken $command,
        $next
    ) {
        /*
         * @var DeleteItems $command
         */
        $this
            ->metadataRepository
            ->deleteItemsMetadata(
                $command->getRepositoryReference(),
                $command->getItemsUUID()
            );

        return $next($command);
    }

    /**
     * Event subscribed namespace.
     *
     * @return string
     */
    public function getSubscribedEvent(): string
    {
        return DeleteItems::class;
    }
}
