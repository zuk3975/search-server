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
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\AddToken;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AddTokenCommand.
 */
class AddTokenCommand extends CommandWithBusAndGodToken
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('apisearch:add-token')
            ->setDescription('Add a token')
            ->addArgument(
                'app-id',
                InputArgument::REQUIRED,
                'App id'
            )
            ->addArgument(
                'uuid',
                InputArgument::OPTIONAL,
                'Token UUID. If none defined, a new one will be generated',
                Uuid::uuid4()->toString()
            )
            ->addOption(
                'index',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Indices',
                []
            )
            ->addOption(
                'http-referrer',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Http referrers',
                []
            )
            ->addOption(
                'endpoint',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Endpoints',
                []
            )
            ->addOption(
                'plugin',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Plugins',
                []
            )
            ->addOption(
                'seconds-valid',
                null,
                InputOption::VALUE_OPTIONAL,
                'Seconds valid',
                Token::INFINITE_DURATION
            )
            ->addOption(
                'max-hits-per-query',
                null,
                InputOption::VALUE_OPTIONAL,
                'Maximum hits per query',
                Token::INFINITE_HITS_PER_QUERY
            )
            ->addOption(
                'ttl',
                null,
                InputOption::VALUE_OPTIONAL,
                'TTL',
                Token::DEFAULT_TTL
            );
    }

    /**
     * Dispatch domain event.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function dispatchDomainEvent(
        InputInterface $input,
        OutputInterface $output
    ) {
        $tokenUUID = TokenUUID::createById($input->getArgument('uuid'));
        $appUUID = AppUUID::createById($input->getArgument('app-id'));

        $this->printInfoMessage(
            $output,
            $this->getHeader(),
            "App ID: <strong>{$appUUID->composeUUID()}</strong>"
        );

        $this->printInfoMessage(
            $output,
            $this->getHeader(),
            "Token UUID: <strong>{$tokenUUID->composeUUID()}</strong>"
        );

        $endpoints = $this->getEndpoints($input, $output);
        $this
            ->commandBus
            ->handle(new AddToken(
                RepositoryReference::create($appUUID),
                $this->createGodToken($appUUID),
                new Token(
                    $tokenUUID,
                    $appUUID,
                    array_map(function (string $index) {
                        return IndexUUID::createById(trim($index));
                    }, $input->getOption('index')),
                    $input->getOption('http-referrer'),
                    $endpoints,
                    $input->getOption('plugin'),
                    (int) $input->getOption('seconds-valid'),
                    (int) $input->getOption('max-hits-per-query'),
                    (int) $input->getOption('ttl')
                )
            ));
    }

    /**
     * Dispatch domain event.
     *
     * @return string
     */
    protected function getHeader(): string
    {
        return 'Add token';
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
        return 'Token added properly';
    }
}
