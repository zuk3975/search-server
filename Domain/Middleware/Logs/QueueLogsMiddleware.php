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

namespace Apisearch\Server\Domain\Middleware\Logs;

use Apisearch\Log\Log;
use Apisearch\Repository\WithRepositoryReference;
use League\Tactician\Middleware;
use RSQueue\Services\Producer as QueueProducer;

/**
 * Class QueueLogsMiddleware.
 */
class QueueLogsMiddleware extends LogsMiddleware implements Middleware
{
    /**
     * @var QueueProducer
     *
     * Queue producer
     */
    private $queueProducer;

    /**
     * DomainEventsMiddleware constructor.
     *
     * @param QueueProducer $queueProducer
     */
    public function __construct(QueueProducer  $queueProducer)
    {
        $this->queueProducer = $queueProducer;
    }

    /**
     * Process log.
     *
     * @param WithRepositoryReference $command
     * @param Log                     $log
     */
    public function processLog(
        WithRepositoryReference $command,
        Log $log
    ) {
        $repositoryReference = $command->getRepositoryReference();
        $this
            ->queueProducer
            ->produce(
                'apisearch:server:logs',
                [
                    'app_uuid' => $repositoryReference
                        ->getAppUUID()
                        ->toArray(),
                    'index_uuid' => $repositoryReference
                        ->getIndexUUID()
                        ->toArray(),
                    'log' => $log->toArray(),
                ]
            );
    }
}
