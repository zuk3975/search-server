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

namespace Apisearch\Server\Domain\Repository\AppRepository;

use Apisearch\Repository\WithRepositoryReference;
use Apisearch\Repository\WithRepositoryReferenceTrait;
use Apisearch\Server\Domain\Token\TokenLocator;
use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;

/**
 * Class InMemoryTokenRepository.
 */
class InMemoryTokenRepository implements TokenRepository, TokenLocator, WithRepositoryReference
{
    use WithRepositoryReferenceTrait;

    /**
     * @var string[]
     *
     * Tokens
     */
    private $tokens = [];

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
     * Add token.
     *
     * @param Token $token
     */
    public function addToken(Token $token)
    {
        if (!isset($this->tokens[$this->getAppId()])) {
            $this->tokens[$this->getAppId()] = [];
        }

        $this->tokens[$this->getAppId()][$token->getTokenUUID()->composeUUID()] = $token;
    }

    /**
     * Delete token.
     *
     * @param TokenUUID $tokenUUID
     */
    public function deleteToken(TokenUUID $tokenUUID)
    {
        if (!isset($this->tokens[$this->getAppId()])) {
            return;
        }

        unset($this->tokens[$this->getAppId()][$tokenUUID->composeUUID()]);
    }

    /**
     * Get tokens.
     *
     * @return Token[]
     */
    public function getTokens(): array
    {
        if (!isset($this->tokens[$this->getAppId()])) {
            return [];
        }

        return $this->tokens[$this->getAppId()];
    }

    /**
     * Delete all tokens.
     */
    public function deleteTokens()
    {
        unset($this->tokens[$this->getAppId()]);
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
        if (!isset($this->tokens[$appId])) {
            return null;
        }

        return $this->tokens[$appId][$tokenReference] ?? null;
    }
}
