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

namespace Apisearch\Server\Domain\EventEnqueuer;

/**
 * Interface EventEnqueuer.
 */
interface EventEnqueuer
{
    /**
     * Enqueue a domain event.
     *
     * @param array $event
     */
    public function enqueueEvent(array $event);
}
