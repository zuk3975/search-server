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

namespace Apisearch\Server\Domain\CommandConsumer;

use Apisearch\Server\Domain\AsynchronousableCommand;
use Apisearch\Server\Domain\Consumer;
use Exception;
use League\Tactician\CommandBus;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CommandConsumer.
 */
class CommandConsumer extends Consumer
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
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * Persist domain event.
     *
     * @param OutputInterface $output
     * @param array           $data
     */
    public function consumeCommand(
        OutputInterface $output,
        array $data
    ) {
        $class = 'Apisearch\Server\Domain\Command\\'.$data['class'];
        if (
            !class_exists($class) ||
            !in_array(AsynchronousableCommand::class, class_implements($class))
        ) {
            return;
        }

        $success = true;
        $message = '';
        $command = $data['class'];
        $from = microtime(true);
        try {
            $this
                ->commandBus
                ->handle($class::fromArray($data));
        } catch (Exception $exception) {
            // Silent pass
            $success = false;
            $message = $exception->getMessage();
        }

        $this->logOutput(
            $output,
            $command,
            $success,
            $message,
            $from
        );
    }
}
