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
use Apisearch\Server\Domain\Command\AddToken;
use Apisearch\Server\Domain\CommandWithRepositoryReferenceAndToken;
use Apisearch\Token\Token;

/**
 * Class AddTokenHttpCommandAdapter.
 */
class AddTokenHttpCommandAdapter implements HttpCommandAdapter
{
    /**
     * Get command namespace.
     *
     * @return string
     */
    public function getCommandNamespace(): string
    {
        return AddToken::class;
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
         * @var AddToken $command
         */
        return [
            Http::TOKEN_FIELD => $command->getNewToken()->toArray(),
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
         * @var AddToken
         */
        $newToken = isset($callbackResponse['body'][Http::TOKEN_FIELD])
            ? Token::createFromArray($callbackResponse['body'][Http::TOKEN_FIELD])
            : $command->getNewToken();

        return new AddToken(
            $command->getRepositoryReference(),
            $command->getToken(),
            $newToken
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
        return null;
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
        return null;
    }
}
