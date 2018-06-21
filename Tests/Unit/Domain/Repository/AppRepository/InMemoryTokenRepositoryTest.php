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

namespace Apisearch\Server\Tests\Unit\Domain\Repository\AppRepository;

use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Repository\AppRepository\InMemoryTokenRepository;
use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;
use PHPUnit\Framework\TestCase;

/**
 * Class InMemoryTokenRepositoryTest.
 */
class InMemoryTokenRepositoryTest extends TestCase
{
    /**
     * Test add and remove token.
     */
    public function testAddRemoveToken()
    {
        $repository = new InMemoryTokenRepository();
        $repository->setRepositoryReference(RepositoryReference::create('yyy', 'index'));
        $tokenUUID = TokenUUID::createById('xxx');
        $token = new Token($tokenUUID, 'yyy');
        $repository->addToken($token);
        $this->assertEquals(
            $token,
            $repository->getTokenByReference('yyy', 'xxx')
        );
        $this->assertNull($repository->getTokenByReference('yyy', 'lll'));
        $repository->deleteToken($tokenUUID);
        $this->assertNull($repository->getTokenByReference('yyy', 'xxx'));
    }

    /**
     * Test delete tokens.
     */
    public function testDeleteTokens()
    {
        $repository = new InMemoryTokenRepository();
        $repository->setRepositoryReference(RepositoryReference::create('yyy', 'index'));
        $tokenUUID = TokenUUID::createById('xxx');
        $token = new Token($tokenUUID, 'yyy');
        $repository->addToken($token);
        $tokenUUID2 = TokenUUID::createById('xxx2');
        $token2 = new Token($tokenUUID2, 'yyy');
        $repository->addToken($token2);
        $repository->setRepositoryReference(RepositoryReference::create('zzz', 'index'));
        $tokenUUID3 = TokenUUID::createById('xxx3');
        $token3 = new Token($tokenUUID3, 'zzz');
        $repository->addToken($token3);

        $repository->setRepositoryReference(RepositoryReference::create('yyy', 'index'));
        $this->assertCount(2, $repository->getTokens());
        $repository->setRepositoryReference(RepositoryReference::create('zzz', 'index'));
        $this->assertCount(1, $repository->getTokens());
        $repository->setRepositoryReference(RepositoryReference::create('lol', 'index'));
        $this->assertCount(0, $repository->getTokens());

        $repository->setRepositoryReference(RepositoryReference::create('yyy', 'index'));
        $repository->deleteTokens();
        $this->assertCount(0, $repository->getTokens());
    }
}
