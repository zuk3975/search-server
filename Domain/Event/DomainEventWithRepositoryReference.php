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

use Apisearch\Repository\RepositoryReference;

/**
 * Class DomainEventWithRepositoryReference.
 */
class DomainEventWithRepositoryReference
{
    /**
     * @var RepositoryReference
     *
     * Repository reference
     */
    private $repositoryReference;

    /**
     * @var DomainEvent
     *
     * Domain event
     */
    private $domainEvent;

    /**
     * @var int
     *
     * Time cost
     */
    private $timeCost;

    /**
     * EventWithRepositoryReference constructor.
     *
     * @param RepositoryReference $repositoryReference
     * @param DomainEvent         $domainEvent
     * @param int                 $timeCost
     */
    public function __construct(
        RepositoryReference $repositoryReference,
        DomainEvent $domainEvent,
        int $timeCost = -1
    ) {
        $this->repositoryReference = $repositoryReference;
        $this->domainEvent = $domainEvent;
        $this->timeCost = $timeCost;
    }

    /**
     * Get RepositoryReference.
     *
     * @return RepositoryReference
     */
    public function getRepositoryReference(): RepositoryReference
    {
        return $this->repositoryReference;
    }

    /**
     * Get DomainEvent.
     *
     * @return DomainEvent
     */
    public function getDomainEvent(): DomainEvent
    {
        return $this->domainEvent;
    }

    /**
     * Get TimeCost.
     *
     * @return int
     */
    public function getTimeCost(): int
    {
        return $this->timeCost;
    }
}
