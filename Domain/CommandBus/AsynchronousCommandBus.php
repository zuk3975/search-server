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

namespace Apisearch\Server\Domain\CommandBus;

use Apisearch\Server\Domain\AsynchronousableCommand;
use League\Tactician\CommandBus;
use RSQueue\Services\Producer;

/**
 * Class AsynchronousCommandBus.
 */
class AsynchronousCommandBus extends CommandBus
{
    /**
     * @var Producer
     *
     * Producer
     */
    private $producer;

    /**
     * @var CommandBus
     *
     * Command bus
     */
    private $commandBus;

    /**
     * AsynchronousCommandIngestor constructor.
     *
     * @param Producer   $producer
     * @param CommandBus $commandBus
     */
    public function __construct(
        Producer $producer,
        CommandBus $commandBus
    ) {
        parent::__construct([]);
        $this->producer = $producer;
    }

    /**
     * Executes the given command and optionally returns a value.
     *
     * @param object $command
     *
     * @return mixed
     */
    public function handle($command)
    {
        if (!$command instanceof AsynchronousableCommand) {
            $this
                ->commandBus
                ->handle($command);

            return;
        }

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
