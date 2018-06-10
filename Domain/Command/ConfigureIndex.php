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

namespace Apisearch\Server\Domain\Command;

use Apisearch\Config\Config;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Repository\WithRepositoryReference;
use Apisearch\Server\Domain\AsynchronousableCommand;
use Apisearch\Server\Domain\CommandWithRepositoryReferenceAndToken;
use Apisearch\Server\Domain\LoggableCommand;
use Apisearch\Server\Domain\WriteCommand;
use Apisearch\Token\Token;

/**
 * Class ConfigureIndex.
 */
class ConfigureIndex extends CommandWithRepositoryReferenceAndToken implements WithRepositoryReference, WriteCommand, LoggableCommand, AsynchronousableCommand
{
    /**
     * @var Config
     *
     * Query
     */
    private $config;

    /**
     * DeleteCommand constructor.
     *
     * @param RepositoryReference $repositoryReference
     * @param Token               $token
     * @param Config              $config
     */
    public function __construct(
        RepositoryReference $repositoryReference,
        Token              $token,
        Config $config
    ) {
        parent::__construct(
            $repositoryReference,
            $token
        );

        $this->config = $config;
    }

    /**
     * Get Query.
     *
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'configuration' => $this
                ->config
                ->toArray(),
            'repository_reference' => [
                'app_id' => $this->getRepositoryReference()->getAppId(),
                'index' => $this->getRepositoryReference()->getIndex(),
            ],
            'token' => $this->getToken()->toArray(),
        ];
    }

    /**
     * Create command from array.
     *
     * @param array $data
     *
     * @return AsynchronousableCommand
     */
    public static function fromArray(array $data): AsynchronousableCommand
    {
        return new self(
            RepositoryReference::create(
                $data['repository_reference']['app_id'],
                $data['repository_reference']['index']
            ),
            Token::createFromArray($data['token']),
            Config::createFromArray($data['configuration'])
        );
    }
}
