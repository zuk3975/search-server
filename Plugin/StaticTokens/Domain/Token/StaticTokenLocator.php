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

namespace Apisearch\Plugin\StaticTokens\Domain\Token;

use Apisearch\Server\Domain\Token\TokenLocator;
use Apisearch\Token\Token;

/**
 * Class TokenRedisRepository.
 */
class StaticTokenLocator implements TokenLocator
{
    /**
     * @var Token[]
     *
     * Tokens
     */
    private $tokens = [];

    /**
     * TokenRedisRepository constructor.
     *
     * @param array[] $tokensAsArray
     */
    public function __construct(array $tokensAsArray)
    {
        $tokens = [];
        array_walk($tokensAsArray, function (array $tokenAsArray, string $key) use (&$tokens) {
            $tokenAsArray['uuid'] = ['id' => $key];
            $tokenAsArray['created_at'] = null;
            $tokenAsArray['updated_at'] = null;
            $tokens[] = Token::createFromArray($tokenAsArray);
        });

        $this->tokens = $tokens;
    }

    /**
     * Locator is enabled.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return true;
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
        $tokens = array_values(
            array_filter(
                $this->tokens,
                function (Token $token) use ($appId, $tokenReference) {
                    return
                        $token->getAppId() === $appId &&
                        $token->getTokenUUID()->getId() === $tokenReference;
                }
            )
        );

        return empty($tokens)
            ? null
            : $tokens[0];
    }
}
