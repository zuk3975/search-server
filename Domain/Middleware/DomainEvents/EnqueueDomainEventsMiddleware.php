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

namespace Apisearch\Server\Domain\Middleware\DomainEvents;

use Apisearch\Model\AppUUID;
use Apisearch\Model\IndexUUID;
use Apisearch\Server\Domain\Event\DomainEventWithRepositoryReference;
use Apisearch\Server\Domain\Event\EventPublisher;
use Apisearch\Server\Domain\EventEnqueuer\EventEnqueuer;
use League\Tactician\Middleware;

/**
 * Class EnqueueDomainEventsMiddleware.
 */
class EnqueueDomainEventsMiddleware extends DomainEventsMiddleware implements Middleware
{
    /**
     * @var EventEnqueuer
     *
     * Event enqueuer
     */
    private $eventEnqueuer;

    /**
     * DomainEventsMiddleware constructor.
     *
     * @param EventPublisher $eventPublisher
     * @param EventEnqueuer  $eventEnqueuer
     */
    public function __construct(
        EventPublisher $eventPublisher,
        EventEnqueuer $eventEnqueuer
    ) {
        parent::__construct($eventPublisher);

        $this->eventEnqueuer = $eventEnqueuer;
    }

    /**
     * Process events.
     *
     * @param DomainEventWithRepositoryReference $domainEventWithRepositoryReference
     */
    public function processEvent(DomainEventWithRepositoryReference $domainEventWithRepositoryReference)
    {
        $repositoryReference = $domainEventWithRepositoryReference->getRepositoryReference();
        $domainEvent = $domainEventWithRepositoryReference->getDomainEvent();
        $appUUID = $repositoryReference->getAppUUID();
        $indexUUID = $repositoryReference->getIndexUUID();

        $this
            ->eventEnqueuer
            ->enqueueEvent(
                [
                    'app_uuid' => $appUUID instanceof AppUUID
                        ? $appUUID->toArray()
                        : null,
                    'index_uuid' => $indexUUID instanceof IndexUUID
                        ? $indexUUID->toArray()
                        : null,
                    'time_cost' => $domainEventWithRepositoryReference->getTimeCost(),
                    'event' => $domainEvent->toArray(),
                ]
            );
    }
}
