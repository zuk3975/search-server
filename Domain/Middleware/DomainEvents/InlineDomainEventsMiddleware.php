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

use Apisearch\Server\Domain\Event\DomainEventWithRepositoryReference;
use Apisearch\Server\Domain\Event\EventPublisher;
use League\Tactician\Middleware;

/**
 * Class InlineDomainEventsMiddleware.
 */
class InlineDomainEventsMiddleware extends DomainEventsMiddleware implements Middleware
{
    /**
     * @var EventPublisher
     *
     * Event publisher
     */
    private $eventPublisher;

    /**
     * DomainEventsMiddleware constructor.
     *
     * @param EventPublisher $inlineEventPublisher
     * @param EventPublisher $eventPublisher
     */
    public function __construct(
        EventPublisher $inlineEventPublisher,
        EventPublisher $eventPublisher
    ) {
        parent::__construct($inlineEventPublisher);

        $this->eventPublisher = $eventPublisher;
    }

    /**
     * Process events.
     *
     * @param DomainEventWithRepositoryReference $domainEventWithRepositoryReference
     */
    public function processEvent(DomainEventWithRepositoryReference $domainEventWithRepositoryReference)
    {
        $this
            ->eventPublisher
            ->publish($domainEventWithRepositoryReference);
    }
}
