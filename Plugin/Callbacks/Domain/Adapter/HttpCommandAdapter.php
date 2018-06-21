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

use Apisearch\Server\Domain\CommandWithRepositoryReferenceAndToken;

/**
 * Interface HttpCommandAdapter.
 */
interface HttpCommandAdapter
{
    /**
     * Get command namespace.
     *
     * @return string
     */
    public function getCommandNamespace(): string;

    /**
     * Build body by command.
     *
     * @param array                                  $callback
     * @param CommandWithRepositoryReferenceAndToken $command
     *
     * @return array
     */
    public function buildBodyByCommand(
        array $callback,
        CommandWithRepositoryReferenceAndToken $command
    ): array;

    /**
     * Build body by command.
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
    ): array;

    /**
     * Change Command after callback response. This method can return both type
     * of elements.
     *
     * - If the command has been changed, and needs to be passed in order to
     *   return inside the command bus, then return the bus
     * - If a result has to be returned directly (bypass == false), then we
     *   should return the result
     *
     * @param array                                  $callback
     * @param CommandWithRepositoryReferenceAndToken $command
     * @param array                                  $callbackResponse
     *
     * @return mixed
     */
    public function changeCommandAfterCallbackResponse(
        array $callback,
        CommandWithRepositoryReferenceAndToken $command,
        array $callbackResponse
    );

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
    );
}
