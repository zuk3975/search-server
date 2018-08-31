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

use Apisearch\Model\AppUUID;
use Apisearch\Model\Coordinate;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Item;
use Apisearch\Query\Query as QueryModel;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Query\Query;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ExportIndexCommand.
 */
class ExportIndexCommand extends CommandWithBusAndGodToken
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setDescription('Export your index items in a portable file')
            ->addArgument(
                'app-id',
                InputArgument::REQUIRED,
                'App id'
            )
            ->addArgument(
                'index',
                InputArgument::REQUIRED,
                'Index name'
            )
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'File'
            );
    }

    /**
     * Dispatch domain event.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed|null
     */
    protected function dispatchDomainEvent(
        InputInterface $input,
        OutputInterface $output
    ) {
        $appUUID = AppUUID::createById($input->getArgument('app-id'));
        $indexUUID = IndexUUID::createById($input->getArgument('index'));
        $file = $input->getArgument('file');
        $resource = fopen($file, 'w');

        $i = 1;
        $itemsNb = 0;
        while (true) {
            $items = $this
                ->commandBus
                ->handle(new Query(
                    RepositoryReference::create(
                        $appUUID,
                        $indexUUID
                    ),
                    $this->createGodToken($appUUID),
                    QueryModel::create('', $i, 100)
                ))
                ->getItems();

            if (empty($items)) {
                break;
            }

            $itemsNb += count($items);
            $this->writeItemsToResource(
                $resource,
                $items,
                $output
            );

            ++$i;
        }

        fclose($resource);

        return $itemsNb;
    }

    /**
     * Dispatch domain event.
     *
     * @return string
     */
    protected function getHeader(): string
    {
        return 'Export index';
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
        return sprintf('Exported %d items from index', $result);
    }

    /**
     * Echo items as CSV.
     *
     * @param resource        $resource
     * @param Item[]          $items
     * @param OutputInterface $output
     */
    private function writeItemsToResource(
        $resource,
        array $items,
        OutputInterface $output
    ) {
        foreach ($items as $item) {
            fputcsv($resource, [
                $item->getId(),
                $item->getType(),
                json_encode($item->getMetadata()),
                json_encode($item->getIndexedMetadata()),
                json_encode($item->getSearchableMetadata()),
                json_encode($item->getExactMatchingMetadata()),
                json_encode($item->getSuggest()),
                json_encode(
                    ($item->getCoordinate() instanceof Coordinate)
                        ? $item->getCoordinate()->toArray()
                        : null
                ),
            ]);
        }

        $this->printInfoMessage(
            $output,
            $this->getHeader(),
            sprintf('Partial export of %d items', count($items))
        );
    }
}
