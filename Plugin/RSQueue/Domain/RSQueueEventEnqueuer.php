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

namespace Apisearch\Plugin\RSQueue\Domain;

use Apisearch\Server\Domain\EventEnqueuer\EventEnqueuer;
use RSQueue\Services\Producer;

/**
 * Class RSQueueEventEnqueuer.
 */
class RSQueueEventEnqueuer implements EventEnqueuer
{
    /**
     * @var Producer
     *
     * Producer
     */
    private $producer;

    /**
     * AsynchronousCommandIngestor constructor.
     *
     * @param Producer $producer
     */
    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    /**
     * Enqueue a domain event.
     *
     * @param array $event
     */
    public function enqueueEvent(array $event)
    {
        $this
            ->producer
            ->produce(
                'events_queue',
                $event
            );
    }
}
