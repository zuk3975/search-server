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

namespace Apisearch\Server\Domain\Token;

use Apisearch\Http\Endpoints;
use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;

/**
 * Class StaticTokenLocator.
 */
class StaticTokenLocator implements TokenLocator
{
    /**
     * @var string
     *
     * God token
     */
    private $godToken;

    /**
     * @var string
     *
     * Readonly token
     */
    private $readonlyToken;

    /**
     * @var string
     *
     * Ping token
     */
    private $pingToken;

    /**
     * TokenValidator constructor.
     *
     * @param string $godToken
     * @param string $readonlyToken
     * @param string $pingToken
     */
    public function __construct(
        string $godToken,
        string $readonlyToken,
        string $pingToken
    ) {
        $this->godToken = $godToken;
        $this->readonlyToken = $readonlyToken;
        $this->pingToken = $pingToken;
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
        if ($tokenReference === $this->godToken) {
            return $this->createGodToken($appId);
        }

        if (
            !empty($this->readonlyToken) &&
            $tokenReference === $this->readonlyToken
        ) {
            return $this->createReadOnlyToken($appId);
        }

        if (
            !empty($this->pingToken) &&
            $tokenReference === $this->pingToken
        ) {
            return $this->createPingToken();
        }

        return null;
    }

    /**
     * Create god token instance.
     *
     * @param string $appId
     *
     * @return Token
     */
    private function createGodToken(string $appId): Token
    {
        return new Token(
            TokenUUID::createById($this->godToken),
            $appId,
            [],
            [],
            [],
            [],
            Token::INFINITE_DURATION,
            Token::INFINITE_HITS_PER_QUERY,
            Token::NO_CACHE
        );
    }

    /**
     * Create read only token instance.
     *
     * @param string $appId
     *
     * @return Token
     */
    private function createReadOnlyToken(string $appId): Token
    {
        return new Token(
            TokenUUID::createById($this->readonlyToken),
            $appId,
            [],
            [],
            Endpoints::compose(Endpoints::queryOnly()),
            [],
            Token::INFINITE_DURATION,
            Token::INFINITE_HITS_PER_QUERY,
            Token::NO_CACHE
        );
    }

    /**
     * Create ping token instance.
     *
     * @return Token
     */
    private function createPingToken(): Token
    {
        return new Token(
            TokenUUID::createById($this->pingToken),
            '',
            [],
            [],
            [
                'head~~/', // Ping
                'get~~/health', // Check health
            ],
            [],
            Token::INFINITE_DURATION,
            Token::INFINITE_HITS_PER_QUERY,
            Token::NO_CACHE
        );
    }
}
