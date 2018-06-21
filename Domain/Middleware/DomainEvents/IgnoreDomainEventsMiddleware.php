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
use League\Tactician\Middleware;

/**
 * Class IgnoreDomainEventsMiddleware.
 */
class IgnoreDomainEventsMiddleware extends DomainEventsMiddleware implements Middleware
{
    /**
     * Process events.
     *
     * @param DomainEventWithRepositoryReference $domainEventWithRepositoryReference
     */
    public function processEvent(DomainEventWithRepositoryReference $domainEventWithRepositoryReference)
    {
        // Silent pass
    }
}
