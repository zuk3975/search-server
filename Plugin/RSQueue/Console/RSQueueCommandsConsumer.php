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

namespace Apisearch\Plugin\RSQueue\Console;

use Apisearch\Server\Domain\CommandConsumer\CommandConsumer;
use RSQueue\Command\ConsumerCommand;
use RSQueue\Services\Consumer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RSQueueCommandsConsumer.
 */
class RSQueueCommandsConsumer extends ConsumerCommand
{
    /**
     * @var CommandConsumer
     *
     * Command consumer
     */
    private $commandConsumer;

    /**
     * ConsumerCommand constructor.
     *
     * @param Consumer        $consumer
     * @param CommandConsumer $commandConsumer
     */
    public function __construct(
        Consumer $consumer,
        CommandConsumer $commandConsumer
    ) {
        parent::__construct($consumer);

        $this->commandConsumer = $commandConsumer;
    }

    /**
     * Definition method.
     *
     * All RSQueue commands must implements its own define() method
     * This method will subscribe command to desired queues
     * with their respective methods
     */
    public function define()
    {
        $this->addQueue('commands_queue', 'consumeCommand');
    }

    /**
     * Persist domain event.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param array           $data
     */
    protected function consumeCommand(
        InputInterface $input,
        OutputInterface $output,
        array $data
    ) {
        $this
            ->commandConsumer
            ->consumeCommand(
                $output,
                $data
            );
    }
}
