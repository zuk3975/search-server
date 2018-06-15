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

use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\DeleteIndex;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DeleteIndexCommand.
 */
class DeleteIndexCommand extends CommandWithBusAndGodToken
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setDescription('Delete an index')
            ->addArgument(
                'app-id',
                InputArgument::REQUIRED,
                'App id'
            )
            ->addArgument(
                'index',
                InputArgument::REQUIRED,
                'Index'
            );
    }

    /**
     * Dispatch domain event.
     *
     * @return string
     */
    protected function getHeader(): string
    {
        return 'Delete index';
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
        try {
            $this
                ->commandBus
                ->handle(new DeleteIndex(
                    RepositoryReference::create(
                        $input->getArgument('app-id'),
                        $input->getArgument('index')
                    ),
                    $this->createGodToken($input->getArgument('app-id'))
                ));
        } catch (ResourceNotAvailableException $exception) {
            $output->writeln('Index not found. Skipping.');
        }
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
        return 'Indices deleted properly';
    }
}
