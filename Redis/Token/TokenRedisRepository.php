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

namespace Apisearch\Server\Redis\Token;

use Apisearch\Repository\WithRepositoryReference;
use Apisearch\Repository\WithRepositoryReferenceTrait;
use Apisearch\Server\Domain\Repository\AppRepository\TokenRepository;
use Apisearch\Server\Domain\Token\TokenLocator;
use Apisearch\Server\Redis\RedisWrapper;
use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;

/**
 * Class TokenRedisRepository.
 */
class TokenRedisRepository implements TokenRepository, TokenLocator, WithRepositoryReference
{
    use WithRepositoryReferenceTrait;

    /**
     * Redis hast id.
     *
     * @var string
     */
    const REDIS_KEY = 'apisearch_tokens';

    /**
     * @var RedisWrapper
     *
     * Redis wrapper
     */
    private $redisWrapper;

    /**
     * TokenRedisRepository constructor.
     *
     * @param RedisWrapper $redisWrapper
     */
    public function __construct(RedisWrapper $redisWrapper)
    {
        $this->redisWrapper = $redisWrapper;
    }

    /**
     * Get composed redis key.
     *
     * @param string $appId
     *
     * @return string
     */
    private function composeRedisKey(string $appId): string
    {
        return $appId.'~~'.self::REDIS_KEY;
    }

    /**
     * Add token.
     *
     * @param Token $token
     */
    public function addToken(Token $token)
    {
        $this
            ->redisWrapper
            ->getClient()
            ->hSet(
                $this->composeRedisKey($this->getAppId()),
                $token->getTokenUUID()->composeUUID(),
                json_encode($token->toArray())
            );
    }

    /**
     * Delete token.
     *
     * @param TokenUUID $tokenUUID
     */
    public function deleteToken(TokenUUID $tokenUUID)
    {
        $this
            ->redisWrapper
            ->getClient()
            ->hDel(
                $this->composeRedisKey($this->getAppId()),
                $tokenUUID->composeUUID()
            );
    }

    /**
     * Get tokens.
     *
     * @return Token[]
     */
    public function getTokens(): array
    {
        $tokens = $this
            ->redisWrapper
            ->getClient()
            ->hGetAll($this->composeRedisKey($this->getAppId()));

        return array_map(function (string $token) {
            return Token::createFromArray(json_decode($token, true));
        }, $tokens);
    }

    /**
     * Delete all tokens.
     */
    public function deleteTokens()
    {
        $this
            ->redisWrapper
            ->getClient()
            ->del($this->composeRedisKey($this->getAppId()));
    }

    /**
     * Get token by reference.
     *
     * @param string $appId
     * @param string $tokenReference
     *
     * @return null|Token
     */
    public function getTokenByReference(
        string $appId,
        string $tokenReference
    ): ? Token {
        $token = $this
            ->redisWrapper
            ->getClient()
            ->hGet(
                $this->composeRedisKey($appId),
                $tokenReference
            );

        return false === $token
            ? null
            : Token::createFromArray(json_decode($token, true));
    }
}
