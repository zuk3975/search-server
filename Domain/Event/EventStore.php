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

namespace Apisearch\Server\Domain\Event;

use Apisearch\Event\Event;
use Apisearch\Event\EventRepository;
use Apisearch\Repository\RepositoryReference;
use Ramsey\Uuid\Uuid;

/**
 * Class EventStore.
 */
class EventStore implements EventSubscriber
{
    /**
     * @var EventRepository
     *
     * Event repository
     */
    private $eventRepository;

    /**
     * EventStore constructor.
     *
     * @param EventRepository $eventRepository
     */
    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * Subscriber should handle event.
     *
     * @param DomainEventWithRepositoryReference $domainEventWithRepositoryReference
     *
     * @return bool
     */
    public function shouldHandleEvent(DomainEventWithRepositoryReference $domainEventWithRepositoryReference): bool
    {
        return true;
    }

    /**
     * Handle event.
     *
     * @param DomainEventWithRepositoryReference $domainEventWithRepositoryReference
     */
    public function handle(DomainEventWithRepositoryReference $domainEventWithRepositoryReference)
    {
        $onlyAppRepositoryReference = RepositoryReference::create(
            $domainEventWithRepositoryReference
                ->getRepositoryReference()
                ->getAppId(),
            ''
        );

        $this
            ->eventRepository
            ->setRepositoryReference($onlyAppRepositoryReference);

        $domainEvent = $domainEventWithRepositoryReference->getDomainEvent();
        $this
            ->eventRepository
            ->save(
                Event::createByPlainData(
                    Uuid::uuid4()->toString(),
                    str_replace(
                        'Apisearch\Server\Domain\Event\\',
                        '',
                        get_class($domainEvent)
                    ),
                    json_encode($domainEvent->readableOnlyToArray()),
                    array_merge(
                        $domainEvent->indexableToArray(),
                        $domainEvent->occurredOnRanges()
                    ),
                    $domainEvent->occurredOn()
                )
            );
    }
}
