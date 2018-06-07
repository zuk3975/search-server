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

namespace Apisearch\Server\Tests\Unit\Domain\Command;

use Apisearch\Model\ItemUUID;
use Apisearch\Model\User;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\AddInteraction;
use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;
use Apisearch\User\Interaction;
use PHPUnit\Framework\TestCase;

/**
 * Class AddInteractionTest.
 */
class AddInteractionTest extends TestCase
{
    /**
     * Test interaction asynchronous implementation.
     */
    public function testAsynchronous()
    {
        $repositoryReference = RepositoryReference::create('main', 'default');
        $token = new Token(TokenUUID::createById('9999'), 'main');
        $interaction = new Interaction(
            new User('123'),
            new ItemUUID('456', 'product'),
            10
        );

        $addInteraction = new AddInteraction(
            $repositoryReference,
            $token,
            $interaction
        );

        $builtAddInteraction = AddInteraction::fromArray($addInteraction->toArray());
        $this->assertEquals(
            $addInteraction,
            $builtAddInteraction
        );

        $this->assertEquals(
            $interaction,
            $builtAddInteraction->getInteraction()
        );

        $this->assertEquals(
            $repositoryReference,
            $builtAddInteraction->getRepositoryReference()
        );

        $this->assertEquals(
            $token,
            $builtAddInteraction->getToken()
        );
    }
}
