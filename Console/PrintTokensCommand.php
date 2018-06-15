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

use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Query\GetTokens;
use Apisearch\Token\Token;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PrintTokensCommand.
 */
class PrintTokensCommand extends CommandWithBusAndGodToken
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setDescription('Print all tokens of an app-id')
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
        $tokens = $this
            ->commandBus
            ->handle(new GetTokens(
                RepositoryReference::create(
                    $input->getArgument('app-id'),
                    '~~~'
                ),
                $this->createGodToken($input->getArgument('app-id'))
            ));

        /**
         * @var Token
         */
        $table = new Table($output);
        $table->setHeaders(['UUID', 'Indices', 'Seconds Valid', 'Max hits per query', 'HTTP Referrers', 'endpoints', 'plugins', 'ttl']);
        foreach ($tokens as $token) {
            $table->addRow([
                $token->getTokenUUID()->composeUUID(),
                implode(', ', $token->getIndices()),
                $token->getSecondsValid(),
                $token->getMaxHitsPerQuery(),
                implode(', ', $token->getHttpReferrers()),
                implode(', ', $token->getEndpoints()),
                implode(', ', $token->getPlugins()),
                $token->getTtl(),
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
        return 'Get tokens';
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
