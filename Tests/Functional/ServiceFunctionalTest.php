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

namespace Apisearch\Server\Tests\Functional;

use Apisearch\Config\Config;
use Apisearch\Config\ImmutableConfig;
use Apisearch\Model\Changes;
use Apisearch\Model\Index;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Model\User;
use Apisearch\Query\Query as QueryModel;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Result\Events;
use Apisearch\Result\Logs;
use Apisearch\Result\Result;
use Apisearch\Server\Domain\Command\AddInteraction;
use Apisearch\Server\Domain\Command\AddToken;
use Apisearch\Server\Domain\Command\CleanEnvironment;
use Apisearch\Server\Domain\Command\ConfigureEnvironment;
use Apisearch\Server\Domain\Command\ConfigureIndex;
use Apisearch\Server\Domain\Command\CreateIndex;
use Apisearch\Server\Domain\Command\DeleteAllInteractions;
use Apisearch\Server\Domain\Command\DeleteIndex;
use Apisearch\Server\Domain\Command\DeleteItems;
use Apisearch\Server\Domain\Command\DeleteToken;
use Apisearch\Server\Domain\Command\DeleteTokens;
use Apisearch\Server\Domain\Command\IndexItems;
use Apisearch\Server\Domain\Command\ResetIndex;
use Apisearch\Server\Domain\Command\UpdateItems;
use Apisearch\Server\Domain\Query\CheckHealth;
use Apisearch\Server\Domain\Query\CheckIndex;
use Apisearch\Server\Domain\Query\GetIndices;
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
     * Save events.
     *
     * @return bool
     */
    protected static function tokensInRedis(): bool
    {
        return false;
    }

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
        return self::getStatic('apisearch_server.query_bus')
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
        return self::getStatic('apisearch_server.command_bus')
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
        self::getStatic('apisearch_server.command_bus')
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
        self::getStatic('apisearch_server.command_bus')
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
        self::getStatic('apisearch_server.command_bus')
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
     * @param string|null $appId
     *
     * @return array|Index[]
     */
    public function getIndices(string $appId = null): array
    {
        return self::getStatic('apisearch_server.query_bus')
            ->handle(new GetIndices($appId));
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
        self::getStatic('apisearch_server.command_bus')
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
        self::getStatic('apisearch_server.command_bus')
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
        return self::getStatic('apisearch_server.query_bus')
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
        self::getStatic('apisearch_server.command_bus')
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
        self::getStatic('apisearch_server.command_bus')
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
        self::getStatic('apisearch_server.command_bus')
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
        return self::getStatic('apisearch_server.query_bus')
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
     * Delete all tokens.
     *
     * @param string $appId
     * @param Token  $token
     */
    public static function deleteTokens(
        string $appId = null,
        Token $token = null
    ) {
        self::getStatic('apisearch_server.command_bus')
            ->handle(new DeleteTokens(
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
     * Query events.
     *
     * @param QueryModel $query
     * @param int|null   $from
     * @param int|null   $to
     * @param string     $appId
     * @param string     $index
     * @param Token      $token
     *
     * @return Events
     */
    public function queryEvents(
        QueryModel $query,
        ?int $from = null,
        ?int $to = null,
        string $appId = null,
        string $index = null,
        Token $token = null
    ): Events {
        return self::getStatic('apisearch_server.query_bus')
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
     * Query logs.
     *
     * @param QueryModel $query
     * @param int|null   $from
     * @param int|null   $to
     * @param string     $appId
     * @param string     $index
     * @param Token      $token
     *
     * @return Logs
     */
    public function queryLogs(
        QueryModel $query,
        ?int $from = null,
        ?int $to = null,
        string $appId = null,
        string $index = null,
        Token $token = null
    ): Logs {
        return self::getStatic('apisearch_server.query_bus')
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
        self::getStatic('apisearch_server.command_bus')
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
        self::getStatic('apisearch_server.command_bus')
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
        return self::getStatic('apisearch_server.query_bus')->handle(new Ping());
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
        return self::getStatic('apisearch_server.query_bus')->handle(new CheckHealth());
    }

    /**
     * Configure environment.
     */
    public static function configureEnvironment()
    {
        self::getStatic('apisearch_server.command_bus')->handle(new ConfigureEnvironment());
    }

    /**
     * Clean environment.
     */
    public static function cleanEnvironment()
    {
        self::getStatic('apisearch_server.command_bus')->handle(new CleanEnvironment());
    }
}
