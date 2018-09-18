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

namespace Apisearch\Server\Domain\CommandEnqueuer;

use RSQueue\Services\Producer;

/**
 * Class CommandEnqueuer.
 */
class CommandEnqueuer
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
     * Enqueue a command.
     *
     * @param object $command
     */
    public function enqueueCommand($command)
    {
        $commandAsArray = $command->toArray();
        $commandAsArray['class'] = str_replace('Apisearch\Server\Domain\Command\\', '', get_class($command));
        $this
            ->producer
            ->produce(
                'apisearch:server:commands',
                $commandAsArray
            );
    }
}
