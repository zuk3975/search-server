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
use Apisearch\Server\Domain\Event\DomainEvent;
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
    private $eventPublisher;

    /**
     * @var EventSubscriberInterface
     *
     * Event subscriber
     */
    private $eventSubscriber;

    /**
     * DomainEventsMiddleware constructor.
     *
     * @param EventPublisher $eventPublisher
     */
    public function __construct(EventPublisher $eventPublisher)
    {
        $this->eventPublisher = $eventPublisher;
        $this->eventSubscriber = new CollectInMemoryDomainEventSubscriber();
        $this
            ->eventPublisher
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
                     ->getEvents() as $event) {
            $this->processEvent(
                $command,
                $event
            );
        }

        $this
            ->eventSubscriber
            ->flushEvents();

        return $result;
    }

    /**
     * Process events.
     *
     * @param WithRepositoryReference $command
     * @param DomainEvent             $event
     */
    abstract public function processEvent(
        WithRepositoryReference $command,
        DomainEvent $event
    );
}
