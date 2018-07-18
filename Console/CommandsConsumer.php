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

namespace Apisearch\Server\Console;

use Apisearch\Server\Domain\AsynchronousableCommand;
use Exception;
use League\Tactician\CommandBus;
use RSQueue\Command\ConsumerCommand;
use RSQueue\Services\Consumer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CommandsConsumer.
 */
class CommandsConsumer extends ConsumerCommand
{
    /**
     * @var CommandBus
     *
     * Command bus
     */
    private $commandBus;

    /**
     * ConsumerCommand constructor.
     *
     * @param Consumer   $consumer
     * @param CommandBus $commandBus
     */
    public function __construct(
        Consumer $consumer,
        CommandBus $commandBus
    ) {
        parent::__construct($consumer);

        $this->commandBus = $commandBus;
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
        $this->addQueue('apisearch:server:commands', 'handleCommand');
    }

    /**
     * Persist domain event.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param array           $data
     */
    protected function handleCommand(
        InputInterface $input,
        OutputInterface $output,
        array $data
    ) {
        $this->printHeader($input, $output);
        $class = 'Apisearch\Server\Domain\Command\\'.$data['class'];
        if (
            !class_exists($class) ||
            !in_array(AsynchronousableCommand::class, class_implements($class))
        ) {
            return;
        }

        $output->write('Consuming '.$class.' ... ');
        try {
            $this
                ->commandBus
                ->handle($class::fromArray($data));
            $output->write('Ok');
        } catch (Exception $e) {
            // Silent pass
            $output->write('Fail ['.$e->getMessage().']');
        }
        $output->writeln('');
    }

    /**
     * Print header.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function printHeader(
        InputInterface $input,
        OutputInterface $output
    ) {
        $output->writeln('========');
        $output->writeln('========');
        $output->writeln('=== Command consumer');
        $output->writeln('=== env = '.$input->getOption('env'));
        $output->writeln('========');
        $output->writeln('========');
    }
}
