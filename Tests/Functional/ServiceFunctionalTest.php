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

namespace Apisearch\Server\Tests\Functional;

use Apisearch\Config\Config;
use Apisearch\Config\ImmutableConfig;
use Apisearch\Model\Changes;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Model\User;
use Apisearch\Query\Query as QueryModel;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Result\Result;
use Apisearch\Server\Domain\Command\AddInteraction;
use Apisearch\Server\Domain\Command\AddToken;
use Apisearch\Server\Domain\Command\ConfigureIndex;
use Apisearch\Server\Domain\Command\CreateEventsIndex;
use Apisearch\Server\Domain\Command\CreateIndex;
use Apisearch\Server\Domain\Command\CreateLogsIndex;
use Apisearch\Server\Domain\Command\DeleteAllInteractions;
use Apisearch\Server\Domain\Command\DeleteEventsIndex;
use Apisearch\Server\Domain\Command\DeleteIndex;
use Apisearch\Server\Domain\Command\DeleteItems;
use Apisearch\Server\Domain\Command\DeleteLogsIndex;
use Apisearch\Server\Domain\Command\DeleteToken;
use Apisearch\Server\Domain\Command\IndexItems;
use Apisearch\Server\Domain\Command\ResetIndex;
use Apisearch\Server\Domain\Command\UpdateItems;
use Apisearch\Server\Domain\Query\CheckHealth;
use Apisearch\Server\Domain\Query\CheckIndex;
use Apisearch\Server\Domain\Query\GetTokens;
use Apisearch\Server\Domain\Query\Ping;
use Apisearch\Server\Domain\Query\Query;
use Apisearch\Server\Domain\Query\QueryEvents;
use Apisearch\Server\Domain\Query\QueryLogs;
use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;
use Apisearch\User\Interaction;

/**
 * Class ServiceFunctionalTest.
 */
abstract class ServiceFunctionalTest extends ApisearchServerBundleFunctionalTest
{
    /**
     * Query using the bus.
     *
     * @param QueryModel $query
     * @param string     $appId
     * @param string     $index
     * @param Token      $token
     *
     * @return Result
     */
    public function query(
        QueryModel $query,
        string $appId = null,
        string $index = null,
        Token $token = null
    ): Result {
        return self::getStatic('tactician.commandbus')
            ->handle(new Query(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    $index ?? self::$index
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    ),
                $query
            ));
    }

    /**
     * Delete using the bus.
     *
     * @param ItemUUID[] $itemsUUID
     * @param string     $appId
     * @param string     $index
     * @param Token      $token
     */
    public function deleteItems(
        array $itemsUUID,
        string $appId = null,
        string $index = null,
        Token $token = null
    ) {
        return self::getStatic('tactician.commandbus')
            ->handle(new DeleteItems(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    $index ?? self::$index
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    ),
                $itemsUUID
            ));
    }

    /**
     * Add items using the bus.
     *
     * @param Item[] $items
     * @param string $appId
     * @param string $index
     * @param Token  $token
     */
    public static function indexItems(
        array $items,
        ?string $appId = null,
        ?string $index = null,
        ?Token $token = null
    ) {
        self::getStatic('tactician.commandbus')
            ->handle(new IndexItems(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    $index ?? self::$index
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    ),
                $items
            ));
    }

    /**
     * Update using the bus.
     *
     * @param QueryModel $query
     * @param Changes    $changes
     * @param string     $appId
     * @param string     $index
     * @param Token      $token
     */
    public function updateItems(
        QueryModel $query,
        Changes $changes,
        string $appId = null,
        string $index = null,
        Token $token = null
    ) {
        self::getStatic('tactician.commandbus')
            ->handle(new UpdateItems(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    $index ?? self::$index
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    ),
                $query,
                $changes
            ));
    }

    /**
     * Reset index using the bus.
     *
     * @param string $appId
     * @param string $index
     * @param Token  $token
     */
    public function resetIndex(
        string $appId = null,
        string $index = null,
        Token $token = null
    ) {
        self::getStatic('tactician.commandbus')
            ->handle(new ResetIndex(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    $index ?? self::$index
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    )
            ));
    }

    /**
     * Create index using the bus.
     *
     * @param string          $appId
     * @param string          $index
     * @param Token           $token
     * @param ImmutableConfig $config
     */
    public static function createIndex(
        string $appId = null,
        string $index = null,
        Token $token = null,
        ImmutableConfig $config = null
    ) {
        self::getStatic('tactician.commandbus')
            ->handle(new CreateIndex(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    $index ?? self::$index
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    ),
                $config ?? ImmutableConfig::createFromArray([])
            ));
    }

    /**
     * Configure index using the bus.
     *
     * @param Config $config
     * @param string $appId
     * @param string $index
     * @param Token  $token
     */
    public function configureIndex(
        Config $config,
        string $appId = null,
        string $index = null,
        Token $token = null
    ) {
        self::getStatic('tactician.commandbus')
            ->handle(new ConfigureIndex(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    $index ?? self::$index
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    ),
                $config
            ));
    }

    /**
     * Check index.
     *
     * @param string $appId
     * @param string $index
     * @param Token  $token
     *
     * @return bool
     */
    public function checkIndex(
        string $appId = null,
        string $index = null,
        Token $token = null
    ): bool {
        return self::getStatic('tactician.commandbus')
            ->handle(new CheckIndex(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    $index ?? self::$index
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    )
            ));
    }

    /**
     * Delete index using the bus.
     *
     * @param string $appId
     * @param string $index
     * @param Token  $token
     */
    public static function deleteIndex(
        string $appId = null,
        string $index = null,
        Token $token = null
    ) {
        self::getStatic('tactician.commandbus')
            ->handle(new DeleteIndex(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    $index ?? self::$index
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    )
            ));
    }

    /**
     * Add token.
     *
     * @param Token  $newToken
     * @param string $appId
     * @param Token  $token
     */
    public static function addToken(
        Token $newToken,
        string $appId = null,
        Token $token = null
    ) {
        self::getStatic('tactician.commandbus')
            ->handle(new AddToken(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    ''
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    ),
                $newToken
            ));
    }

    /**
     * Delete token.
     *
     * @param TokenUUID $tokenUUID
     * @param string    $appId
     * @param Token     $token
     */
    public static function deleteToken(
        TokenUUID $tokenUUID,
        string $appId = null,
        Token $token = null
    ) {
        self::getStatic('tactician.commandbus')
            ->handle(new DeleteToken(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    ''
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    ),
                $tokenUUID
            ));
    }

    /**
     * Get tokens.
     *
     * @param string $appId
     * @param Token  $token
     *
     * @return Token[]
     */
    public static function getTokens(
        string $appId = null,
        Token $token = null
    ) {
        self::getStatic('tactician.commandbus')
            ->handle(new GetTokens(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    ''
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    )
            ));
    }

    /**
     * Create event index using the bus.
     *
     * @param string $appId
     * @param string $index
     * @param Token  $token
     */
    public static function createEventsIndex(
        string $appId = null,
        string $index = null,
        Token $token = null
    ) {
        self::getStatic('tactician.commandbus')
            ->handle(new CreateEventsIndex(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    $index ?? self::$index
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    )
            ));
    }

    /**
     * Delete event index using the bus.
     *
     * @param string $appId
     * @param string $index
     * @param Token  $token
     */
    public static function deleteEventsIndex(
        string $appId = null,
        string $index = null,
        Token $token = null
    ) {
        self::getStatic('tactician.commandbus')
            ->handle(new DeleteEventsIndex(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    $index ?? self::$index
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    )
            ));
    }

    /**
     * Query events.
     *
     * @param QueryModel $query
     * @param int|null   $from
     * @param int|null   $to
     * @param string     $appId
     * @param string     $index
     * @param Token      $token
     */
    public function queryEvents(
        QueryModel $query,
        ?int $from = null,
        ?int $to = null,
        string $appId = null,
        string $index = null,
        Token $token = null
    ) {
        return self::getStatic('tactician.commandbus')
            ->handle(new QueryEvents(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    $index ?? self::$index
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    ),
                $query,
                $from,
                $to
            ));
    }

    /**
     * Create log index using the bus.
     *
     * @param string $appId
     * @param string $index
     * @param Token  $token
     */
    public static function createLogsIndex(
        string $appId = null,
        string $index = null,
        Token $token = null
    ) {
        self::getStatic('tactician.commandbus')
            ->handle(new CreateLogsIndex(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    $index ?? self::$index
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    )
            ));
    }

    /**
     * Delete log index using the bus.
     *
     * @param string $appId
     * @param string $index
     * @param Token  $token
     */
    public static function deleteLogsIndex(
        string $appId = null,
        string $index = null,
        Token $token = null
    ) {
        self::getStatic('tactician.commandbus')
            ->handle(new DeleteLogsIndex(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    $index ?? self::$index
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    )
            ));
    }

    /**
     * Query logs.
     *
     * @param QueryModel $query
     * @param int|null   $from
     * @param int|null   $to
     * @param string     $appId
     * @param string     $index
     * @param Token      $token
     */
    public function queryLogs(
        QueryModel $query,
        ?int $from = null,
        ?int $to = null,
        string $appId = null,
        string $index = null,
        Token $token = null
    ) {
        return self::getStatic('tactician.commandbus')
            ->handle(new QueryLogs(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    $index ?? self::$index
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    ),
                $query,
                $from,
                $to
            ));
    }

    /**
     * Add interaction.
     *
     * @param string $userId
     * @param string $itemUUIDComposed
     * @param int    $weight
     * @param string $appId
     * @param Token  $token
     */
    public function addInteraction(
        string $userId,
        string $itemUUIDComposed,
        int $weight,
        string $appId,
        Token $token
    ) {
        self::getStatic('tactician.commandbus')
            ->handle(new AddInteraction(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    ''
                ),
                $token,
                new Interaction(
                    new User($userId),
                    ItemUUID::createByComposedUUID($itemUUIDComposed),
                    $weight
                )
            ));
    }

    /**
     * Delete all interactions.
     *
     * @param string $appId
     * @param Token  $token
     */
    public static function deleteAllInteractions(
        string $appId,
        Token $token = null
    ) {
        self::getStatic('tactician.commandbus')
            ->handle(new DeleteAllInteractions(
                RepositoryReference::create(
                    $appId ?? self::$appId,
                    ''
                ),
                $token ??
                    new Token(
                        TokenUUID::createById(self::getParameterStatic('apisearch_server.god_token')),
                        $appId ?? self::$appId
                    )
            ));
    }

    /**
     * Ping.
     *
     * @param Token $token
     *
     * @return bool
     */
    public function ping(Token $token = null): bool
    {
        return self::getStatic('tactician.commandbus')->handle(new Ping());
    }

    /**
     * Check health.
     *
     * @param Token $token
     *
     * @return array
     */
    public function checkHealth(Token $token = null): array
    {
        return self::getStatic('tactician.commandbus')->handle(new CheckHealth());
    }
}
