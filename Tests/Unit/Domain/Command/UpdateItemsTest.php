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
use Apisearch\Model\Changes;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;
use Apisearch\Query\Query;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\UpdateItems;
use PHPUnit\Framework\TestCase;

/**
 * Class UpdateItemsTest.
 */
class UpdateItemsTest extends TestCase
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
        $query = Query::createMatchAll();
        $changes = Changes::create();

        $command = new UpdateItems(
            $repositoryReference,
            $token,
            $query,
            $changes
        );

        $builtCommand = UpdateItems::fromArray($command->toArray());
        $this->assertEquals(
            $command,
            $builtCommand
        );

        $this->assertEquals(
            $query,
            $builtCommand->getQuery()
        );

        $this->assertEquals(
            $changes,
            $builtCommand->getChanges()
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
