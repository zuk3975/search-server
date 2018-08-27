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

use Apisearch\Server\Domain\Query\CheckHealth;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckHealthCommand.
 */
class CheckHealthCommand extends CommandWithBusAndGodToken
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setDescription('Check health');
    }

    /**
     * Dispatch domain event.
     *
     * @return string
     */
    protected function getHeader(): string
    {
        return 'Check health';
    }

    /**
     * Dispatch domain event.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function dispatchDomainEvent(InputInterface $input, OutputInterface $output)
    {
        $health = $this
            ->commandBus
            ->handle(new CheckHealth());

        $this->printInfoMessage($output, 'Memory Used', $health['process']['memory_used']);
        $this->printInfoMessage($output, 'Plugins', implode(', ', array_keys($health['info']['plugins'])));
        $this->printInfoMessage($output, 'Elasticsearch', $health['status']['elasticsearch']);
        $this->printInfoMessage($output, 'Redis', '1' == $health['status']['redis']
            ? 'Ok'
            : 'Error'
        );

        return true === $health['healthy']
            ? 0
            : 1;
    }

    /**
     * Get success message.
     *
     * @param InputInterface $input
     * @param mixed          $result
     *
     * @return string
     */
    protected function getSuccessMessage(
        InputInterface $input,
        $result
    ): string {
        return '';
    }
}
