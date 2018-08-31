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

namespace Apisearch\Server\Tests\Unit\Domain\Command;

use Apisearch\Model\AppUUID;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\DeleteToken;
use PHPUnit\Framework\TestCase;

/**
 * Class DeleteTokenTest.
 */
class DeleteTokenTest extends TestCase
{
    /**
     * Test interaction asynchronous implementation.
     */
    public function testAsynchronous()
    {
        $appUUID = AppUUID::createById('main');
        $indexUUID = IndexUUID::createById('default');
        $repositoryReference = RepositoryReference::create(
            $appUUID,
            $indexUUID
        );
        $token = new Token(TokenUUID::createById('9999'), $appUUID);
        $tokenUUIDToDelete = TokenUUID::createById('5555');

        $command = new DeleteToken(
            $repositoryReference,
            $token,
            $tokenUUIDToDelete
        );

        $builtCommand = DeleteToken::fromArray($command->toArray());
        $this->assertEquals(
            $command,
            $builtCommand
        );

        $this->assertEquals(
            $tokenUUIDToDelete,
            $builtCommand->getTokenUUIDToDelete()
        );

        $this->assertEquals(
            $repositoryReference,
            $builtCommand->getRepositoryReference()
        );

        $this->assertEquals(
            $token,
            $builtCommand->getToken()
        );
    }
}
