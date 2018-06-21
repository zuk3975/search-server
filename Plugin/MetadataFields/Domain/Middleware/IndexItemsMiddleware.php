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
use Apisearch\Server\Domain\Command\IndexItems;
use Apisearch\Server\Domain\CommandWithRepositoryReferenceAndToken;
use Apisearch\Server\Domain\Plugin\PluginMiddleware;

/**
 * Class IndexItemsMiddleware.
 */
class IndexItemsMiddleware implements PluginMiddleware
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
         * @var IndexItems $command
         */
        $this
            ->metadataRepository
            ->saveItemsMetadata(
                $command->getRepositoryReference(),
                $command->getItems()
            );

        return $next($command);
    }

    /**
     * Events subscribed namespace.
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [IndexItems::class];
    }
}
