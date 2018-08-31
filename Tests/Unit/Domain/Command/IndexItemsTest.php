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
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\IndexItems;
use PHPUnit\Framework\TestCase;

/**
 * Class IndexItemsTest.
 */
class IndexItemsTest extends TestCase
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
        $items = [
            Item::create(ItemUUID::createByComposedUUID('123~product')),
            Item::create(ItemUUID::createByComposedUUID('456~product')),
            Item::create(ItemUUID::createByComposedUUID('123~lala')),
        ];

        $command = new IndexItems(
            $repositoryReference,
            $token,
            $items
        );

        $builtCommand = IndexItems::fromArray($command->toArray());
        $this->assertEquals(
            $command,
            $builtCommand
        );

        $this->assertEquals(
            $items,
            $builtCommand->getItems()
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
