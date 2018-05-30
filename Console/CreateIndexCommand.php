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

use Apisearch\Config\ImmutableConfig;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\CreateEventsIndex;
use Apisearch\Server\Domain\Command\CreateIndex;
use Apisearch\Server\Domain\Command\CreateLogsIndex;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateIndexCommand.
 */
class CreateIndexCommand extends CommandWithBusAndGodToken
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setDescription('Create an index')
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
                'language',
                null,
                InputOption::VALUE_OPTIONAL,
                'Index language',
                null
            )
            ->addOption(
                'no-store-searchable-metadata',
                null,
                InputOption::VALUE_NONE,
                'Store searchable metadata'
            )
            ->addOption(
                'with-events',
                null,
                InputOption::VALUE_NONE,
                'Create events as well'
            )
            ->addOption(
                'with-logs',
                null,
                InputOption::VALUE_NONE,
                'Create logs as well'
            )
            ->addOption(
                'synonym',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Synonym'
            );
    }

    /**
     * Dispatch domain event.
     *
     * @return string
     */
    protected function getHeader(): string
    {
        return 'Create index';
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
                ->handle(new CreateIndex(
                    RepositoryReference::create(
                        $input->getArgument('app-id'),
                        $input->getArgument('index')
                    ),
                    $this->createGodToken($input->getArgument('app-id')),
                    ImmutableConfig::createFromArray([
                        'language' => $input->getOption('language'),
                        'store_searchable_metadata' => !$input->getOption('no-store-searchable-metadata'),
                        'synonyms' => array_map(function (string $synonym) {
                            return ['words' => array_map('trim', explode(',', $synonym))];
                        }, $input->getOption('synonym')),
                    ])
                ));
        } catch (ResourceNotAvailableException $exception) {
            $this->printInfoMessage(
                $output,
                $this->getHeader(),
                'Index is already created. Skipping.'
            );
        }

        if ($input->getOption('with-events')) {
            $this->createEvents(
                $input->getArgument('app-id'),
                $input->getArgument('index'),
                $output
            );
        }

        if ($input->getOption('with-logs')) {
            $this->createLogs(
                $input->getArgument('app-id'),
                $input->getArgument('index'),
                $output
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
        return 'Indices created properly';
    }

    /**
     * Create events index.
     *
     * @param string          $appId
     * @param string          $index
     * @param OutputInterface $output
     */
    protected function createEvents(
        string $appId,
        string $index,
        OutputInterface $output
    ) {
        try {
            $this
                ->commandBus
                ->handle(new CreateEventsIndex(
                    RepositoryReference::create(
                        $appId,
                        $index
                    ),
                    $this->createGodToken($appId)
                ));
        } catch (ResourceNotAvailableException $exception) {
            $this->printInfoMessage(
                $output,
                $this->getHeader(),
                'Events index is already created. Skipping.'
            );
        }
    }

    /**
     * Create logs index.
     *
     * @param string          $appId
     * @param string          $index
     * @param OutputInterface $output
     */
    protected function createLogs(
        string $appId,
        string $index,
        OutputInterface $output
    ) {
        try {
            $this
                ->commandBus
                ->handle(new CreateLogsIndex(
                    RepositoryReference::create(
                        $appId,
                        $index
                    ),
                    $this->createGodToken($appId)
                ));
        } catch (ResourceNotAvailableException $exception) {
            $this->printInfoMessage(
                $output,
                $this->getHeader(),
                'Logs index is already created. Skipping.'
            );
        }
    }
}
