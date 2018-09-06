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
use Apisearch\Model\AppUUID;
use Apisearch\Model\IndexUUID;
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
        $appUUID = AppUUID::createById($input->getArgument('app-id'));
        $indexUUID = IndexUUID::createById($input->getArgument('index'));
        try {
            $this
                ->commandBus
                ->handle(new DeleteIndex(
                    RepositoryReference::create(
                        $appUUID,
                        $indexUUID
                    ),
                    $this->createGodToken($appUUID),
                    $indexUUID
                ));
        } catch (ResourceNotAvailableException $exception) {
            $this->printInfoMessage(
                $output,
                $this->getHeader(),
                'Index not found. Skipping.'
            );
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
        return 'Index deleted properly';
    }
}
