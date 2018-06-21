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

use Apisearch\Repository\WithRepositoryReference;
use Apisearch\Server\Domain\Event\CollectInMemoryDomainEventSubscriber;
use Apisearch\Server\Domain\Event\DomainEventWithRepositoryReference;
use Apisearch\Server\Domain\Event\EventPublisher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DomainEventsMiddleware.
 */
abstract class DomainEventsMiddleware
{
    /**
     * @var EventPublisher
     *
     * Event publisher
     */
    private $inlineEventPublisher;

    /**
     * @var EventSubscriberInterface
     *
     * Event subscriber
     */
    private $eventSubscriber;

    /**
     * DomainEventsMiddleware constructor.
     *
     * @param EventPublisher $inlineEventPublisher
     */
    public function __construct(EventPublisher $inlineEventPublisher)
    {
        $this->inlineEventPublisher = $inlineEventPublisher;
        $this->eventSubscriber = new CollectInMemoryDomainEventSubscriber();
        $this
            ->inlineEventPublisher
            ->subscribe($this->eventSubscriber);
    }

    /**
     * @param WithRepositoryReference $command
     * @param callable                $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        $this
            ->eventSubscriber
            ->flushEvents();

        $result = $next($command);

        foreach ($this
                     ->eventSubscriber
                     ->getEvents() as $domainEventWithRepositoryReference) {
            $this->processEvent($domainEventWithRepositoryReference);
        }

        $this
            ->eventSubscriber
            ->flushEvents();

        return $result;
    }

    /**
     * Process events.
     *
     * @param DomainEventWithRepositoryReference $domainEventWithRepositoryReference
     */
    abstract public function processEvent(DomainEventWithRepositoryReference $domainEventWithRepositoryReference);
}
