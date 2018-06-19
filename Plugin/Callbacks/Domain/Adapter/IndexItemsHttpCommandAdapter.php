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
use Apisearch\Model\Item;
use Apisearch\Server\Domain\Command\IndexItems;
use Apisearch\Server\Domain\CommandWithRepositoryReferenceAndToken;

/**
 * Class IndexItemsHttpCommandAdapter.
 */
class IndexItemsHttpCommandAdapter implements HttpCommandAdapter
{
    /**
     * Get command namespace.
     *
     * @return string
     */
    public function getCommandNamespace(): string
    {
        return IndexItems::class;
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
         * @var IndexItems $command
         */
        return [
            Http::ITEMS_FIELD => array_map(function (Item $item) {
                return $item->toArray();
            }, $command->getItems()),
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
         * @var IndexItems
         */
        $items = isset($callbackResponse['body'][Http::ITEMS_FIELD])
            ? array_map(function (array $itemAsArray) {
                return Item::createFromArray($itemAsArray);
            }, $callbackResponse['body'][Http::ITEMS_FIELD])
            : $command->getItems();

        return new IndexItems(
            $command->getRepositoryReference(),
            $command->getToken(),
            $items
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
