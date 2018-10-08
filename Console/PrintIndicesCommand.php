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
use Apisearch\Model\Token;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Query\GetIndices;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PrintTokensCommand.
 */
class PrintIndicesCommand extends CommandWithBusAndGodToken
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setDescription('Print all indices of an app-id')
            ->addArgument(
                'app-id',
                InputArgument::REQUIRED,
                'App id'
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
        $godToken = $this->createGodToken($appUUID);

        $indices = $this
            ->commandBus
            ->handle(new GetIndices(
                RepositoryReference::create($appUUID),
                $godToken
            ));

        /**
         * @var Token
         */
        $table = new Table($output);
        $table->setHeaders(['UUID', 'App ID', 'Doc Count', 'Size', 'Ok?', 'shards', 'replicas']);

        /*
         * @var Index
         */
        foreach ($indices as $index) {
            $table->addRow([
                $index->getUUID()->composeUUID(),
                $index->getAppUUID()->composeUUID(),
                $index->getDocCount(),
                $index->getSize(),
                $index->isOK()
                    ? 'Yes'
                    : 'No',
                $index->getShards(),
                $index->getReplicas(),
            ]);
        }
        $table->render();
    }

    /**
     * Dispatch domain event.
     *
     * @return string
     */
    protected function getHeader(): string
    {
        return 'Get indices';
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
