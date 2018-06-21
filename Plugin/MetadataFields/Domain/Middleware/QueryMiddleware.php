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
use Apisearch\Result\Result;
use Apisearch\Server\Domain\CommandWithRepositoryReferenceAndToken;
use Apisearch\Server\Domain\Plugin\PluginMiddleware;
use Apisearch\Server\Domain\Query\Query;

/**
 * Class QueryMiddleware.
 */
class QueryMiddleware implements PluginMiddleware
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
        /**
         * @var Result
         * @var Query  $command
         */
        $result = $next($command);

        $this
            ->metadataRepository
            ->loadItemsMetadata(
                $command->getRepositoryReference(),
                $result->getItems()
            );

        return $result;
    }

    /**
     * Events subscribed namespace.
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [Query::class];
    }
}
