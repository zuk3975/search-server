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
 * @author PuntMig Technologies
 */

declare(strict_types=1);

namespace Apisearch\Server\Console;

use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Query\CheckIndex;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckIndexCommand.
 */
class CheckIndexCommand extends CommandWithBusAndGodToken
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('apisearch:check-index')
            ->setDescription('Checks an index')
            ->addArgument(
                'app-id',
                InputArgument::REQUIRED,
                'App id'
            )
            ->addArgument(
                'index',
                InputArgument::REQUIRED,
                'Index'
            )
            ->addOption(
                'token',
                null,
                InputOption::VALUE_OPTIONAL,
                'Token UUID'
            );
    }

    /**
     * Dispatch domain event.
     *
     * @return string
     */
    protected function getHeader(): string
    {
        return 'Check index';
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
        $tokenUUID = $input->hasOption('token') && !empty($input->getOption('token'))
            ? $input->getOption('token')
            : $this->godToken;

        $this
            ->commandBus
            ->handle(new CheckIndex(
                RepositoryReference::create(
                    $input->getArgument('app-id'),
                    $input->getArgument('index')
                ),
                $this->createToken(
                    $tokenUUID,
                    $input->getArgument('app-id')
                )
            ));
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
        return $result
            ? 'Index available'
            : 'Index not available with given configuration';
    }
}
