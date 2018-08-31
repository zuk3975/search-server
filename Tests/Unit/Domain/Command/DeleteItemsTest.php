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
use Apisearch\Model\ItemUUID;
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\DeleteItems;
use PHPUnit\Framework\TestCase;

/**
 * Class DeleteItemsTest.
 */
class DeleteItemsTest extends TestCase
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
        $itemsUUID = [
            ItemUUID::createByComposedUUID('123~product'),
            ItemUUID::createByComposedUUID('456~product'),
            ItemUUID::createByComposedUUID('123~lala'),
        ];

        $command = new DeleteItems(
            $repositoryReference,
            $token,
            $itemsUUID
        );

        $builtCommand = DeleteItems::fromArray($command->toArray());
        $this->assertEquals(
            $command,
            $builtCommand
        );

        $this->assertEquals(
            $itemsUUID,
            $builtCommand->getItemsUUID()
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
