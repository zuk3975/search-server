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
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Item;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\IndexItems;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ImportIndexCommand.
 */
class ImportIndexCommand extends CommandWithBusAndGodToken
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setDescription('Import items from a file to your index')
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
        $itemsBuffer = [];
        $itemsNb = 0;

        if (false !== ($handle = fopen($file, 'r'))) {
            while (false !== ($data = fgetcsv($handle, 0, ','))) {
                $itemAsArray = [
                    'uuid' => [
                        'id' => $data[0],
                        'type' => $data[1],
                    ],
                    'metadata' => json_decode($data[2], true),
                    'indexed_metadata' => json_decode($data[3], true),
                    'searchable_metadata' => json_decode($data[4], true),
                    'exact_matching_metadata' => json_decode($data[5], true),
                    'suggest' => json_decode($data[6], true),
                ];

                if (is_array($data[7])) {
                    $itemAsArray['coordinate'] = $data[7];
                }

                $item = Item::createFromArray($itemAsArray);
                $itemsBuffer[] = $item;
                ++$itemsNb;

                if (count($itemsBuffer) >= 500) {
                    $this->saveItems(
                        $appUUID,
                        $indexUUID,
                        $itemsBuffer,
                        $output
                    );

                    $itemsBuffer = [];
                }
            }

            $this->saveItems(
                $appUUID,
                $indexUUID,
                $itemsBuffer,
                $output
            );
        }

        return $itemsNb;
    }

    /**
     * Dispatch domain event.
     *
     * @return string
     */
    protected function getHeader(): string
    {
        return 'Import index';
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
        return sprintf('Imported %d items into index', $result);
    }

    /**
     * Save array of items.
     *
     * @param AppUUID         $appUUID
     * @param IndexUUID       $indexUUID
     * @param Item[]          $items
     * @param OutputInterface $output
     */
    private function saveItems(
        AppUUID $appUUID,
        IndexUUID $indexUUID,
        array $items,
        OutputInterface $output
    ) {
        if (empty($items)) {
            return;
        }

        $this
            ->commandBus
            ->handle(new IndexItems(
                RepositoryReference::create(
                    $appUUID,
                    $indexUUID
                ),
                $this->createGodToken($appUUID),
                $items
            ));

        $this->printInfoMessage(
            $output,
            $this->getHeader(),
            sprintf('Partial import of %d items', count($items))
        );
    }
}
