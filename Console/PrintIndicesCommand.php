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

use Apisearch\Server\Domain\Query\GetIndices;
use Apisearch\Token\Token;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->addOption(
                'app-id',
                'a',
                InputOption::VALUE_OPTIONAL
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
        $appId = $input->getOption('app-id');

        $indices = $this
            ->commandBus
            ->handle(new GetIndices(
                $appId
            ));

        /**
         * @var Token
         */
        $table = new Table($output);
        $table->setHeaders(['AppId', 'Name', 'Doc Count']);
        foreach ($indices as $index) {
            $table->addRow([
                $index->getAppId(),
                $index->getName(),
                $index->getDocCount(),
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
