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

namespace Apisearch\Plugin\Callbacks\Domain\Adapter;

use Apisearch\Http\Http;
use Apisearch\Query\Query as QueryModel;
use Apisearch\Result\Result;
use Apisearch\Server\Domain\CommandWithRepositoryReferenceAndToken;
use Apisearch\Server\Domain\Query\Query;

/**
 * Class QueryHttpCommandAdapter.
 */
class QueryHttpCommandAdapter implements HttpCommandAdapter
{
    /**
     * Get command namespace.
     *
     * @return string
     */
    public function getCommandNamespace(): string
    {
        return Query::class;
    }

    /**
     * Build body by command.
     *
     *
     * @param array                                  $callback
     * @param CommandWithRepositoryReferenceAndToken $command
     *
     * @return array
     */
    public function buildBodyByCommand(
        array $callback,
        CommandWithRepositoryReferenceAndToken $command
    ): array {
        /*
         * @var Query $command
         */
        return [
            Http::QUERY_FIELD => $command->getQuery()->toArray(),
        ];
    }

    /**
     * Change Command after callback response.
     *
     * @param array                                  $callback
     * @param CommandWithRepositoryReferenceAndToken $command
     * @param array                                  $callbackResponse
     *
     * @return CommandWithRepositoryReferenceAndToken
     */
    public function changeCommandAfterCallbackResponse(
        array $callback,
        CommandWithRepositoryReferenceAndToken $command,
        array $callbackResponse
    ): CommandWithRepositoryReferenceAndToken {
        /**
         * @var Query
         */
        $query = isset($callbackResponse['body'][Http::QUERY_FIELD])
            ? QueryModel::createFromArray($callbackResponse['body'][Http::QUERY_FIELD])
            : $command->getQuery();

        return new Query(
            $command->getRepositoryReference(),
            $command->getToken(),
            $query
        );
    }

    /**
     * Build body by command.
     *
     *
     * @param array                                  $callback
     * @param CommandWithRepositoryReferenceAndToken $command
     * @param mixed                                  $response
     *
     * @return array
     */
    public function buildBodyByCommandAndResponse(
        array $callback,
        CommandWithRepositoryReferenceAndToken $command,
        $response
    ): array {
        /*
         * @var Result $response
         */
        return $response->toArray();
    }

    /**
     * Change Response after callback response.
     *
     * @param array                                  $callback
     * @param CommandWithRepositoryReferenceAndToken $command
     * @param array                                  $callbackResponse
     * @param mixed                                  $originalResponse
     *
     * @return mixed
     */
    public function changeResponseAfterCallbackResponse(
        array $callback,
        CommandWithRepositoryReferenceAndToken $command,
        array $callbackResponse,
        $originalResponse
    ) {
        return empty($callbackResponse['body'])
            ? $originalResponse
            : Result::createFromArray($callbackResponse['body']);
    }
}
