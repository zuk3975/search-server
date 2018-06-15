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

use Apisearch\Config\Config;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\ConfigureIndex;
use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigureIndexTest.
 */
class ConfigureIndexTest extends TestCase
{
    /**
     * Test interaction asynchronous implementation.
     */
    public function testAsynchronous()
    {
        $repositoryReference = RepositoryReference::create('main', 'default');
        $token = new Token(TokenUUID::createById('9999'), 'main');
        $configuration = new Config();

        $configureIndex = new ConfigureIndex(
            $repositoryReference,
            $token,
            $configuration
        );

        $builtConfigureIndex = ConfigureIndex::fromArray($configureIndex->toArray());
        $this->assertEquals(
            $configureIndex,
            $builtConfigureIndex
        );

        $this->assertEquals(
            $configuration,
            $builtConfigureIndex->getConfig()
        );

        $this->assertEquals(
            $repositoryReference,
            $builtConfigureIndex->getRepositoryReference()
        );

        $this->assertEquals(
            $token,
            $builtConfigureIndex->getToken()
        );
    }
}
