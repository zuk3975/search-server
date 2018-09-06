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
use Apisearch\Query\Query as ModelQuery;
use Apisearch\Model\AppUUID;
use Apisearch\Model\IndexUUID;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Result\Result;
use Apisearch\Server\Domain\Query\Query;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class QueryCommand.
 */
class QueryCommand extends CommandWithBusAndGodToken
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
            ->addArgument(
                'query',
                InputArgument::OPTIONAL,
                'Query text',
                ''
            )
            ->addOption(
                'page',
                null,
                InputOption::VALUE_OPTIONAL,
                'Page',
                ModelQuery::DEFAULT_PAGE
            )
            ->addOption(
                'size',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number of results',
                ModelQuery::DEFAULT_SIZE
            );
    }

    /**
     * Dispatch domain event.
     *
     * @return string
     */
    protected function getHeader(): string
    {
        return 'Query index';
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
        $query = $input->getArgument('query');

        $this->printInfoMessage(
            $output,
            $this->getHeader(),
            "App ID: <strong>{$appUUID->composeUUID()}</strong>"
        );

        $this->printInfoMessage(
            $output,
            $this->getHeader(),
            "Index ID: <strong>{$indexUUID->composeUUID()}</strong>"
        );

        $this->printInfoMessage(
            $output,
            'Query / Page / Size',
            sprintf('<strong>%s</strong> / %d / %d',
                $query === ''
                    ? '*'
                    : $query,
                $input->getOption('page'),
                $input->getOption('size')
            )
        );

        try {
            $result = $this
                ->commandBus
                ->handle(new Query(
                    RepositoryReference::create(
                        $appUUID,
                        $indexUUID
                    ),
                    $this->createGodToken($appUUID),
                    ModelQuery::create(
                        $input->getArgument('query'),
                        $input->getOption('page'),
                        $input->getOption('size')
                    )
                ));

            $this->printResult($output, $result);
        } catch (ResourceNotAvailableException $exception) {
            $this->printInfoMessage(
                $output,
                $this->getHeader(),
                $output->writeln('Index not found. Skipping.')
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
        return '';
    }

    /**
     * Print results
     *
     * @param OutputInterface $output
     * @param Result $result
     */
    private function printResult(
        OutputInterface $output,
        Result $result
    )
    {
        $this->printInfoMessage(
            $output,
            'Number of resources in index',
            $result->getTotalItems()
        );

        $this->printInfoMessage(
            $output,
            'Number of hits',
            $result->getTotalHits()
        );

        $i = 1;
        foreach ($result->getItems() as $item) {
            $firstStringPosition = array_reduce($item->getAllMetadata(), function($carry, $element) {
                return is_string($carry)
                    ? $carry
                    : (
                        is_string($element)
                            ? $element
                            : null
                    );
            }, null);
            $this->printInfoMessage(
                $output,
                '    #' . $i,
                sprintf('%s - %s',
                    $item->getUUID()->composeUUID(),
                    substr((string) $firstStringPosition, 0, 50)
                )
            );
        }
    }
}
