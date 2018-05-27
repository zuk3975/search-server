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

namespace Apisearch\Server\Tests\Functional\Console;

use Symfony\Component\Console\Input\ArrayInput;

/**
 * Class CreateTokenCommandTest.
 */
class CreateTokenCommandTest extends CommandTest
{
    /**
     * Test token creation.
     */
    public function testTokenCreation()
    {
        static::$application->run(new ArrayInput(
            [
                'command' => 'apisearch:create-index',
                'app-id' => self::$appId,
                'index' => self::$index,
                '--quiet' => true,
            ]
        ));

        $this->assertTokenNotExists();

        static::$application->run(new ArrayInput(
            [
                'command' => 'apisearch:add-token',
                'uuid' => $this->token,
                'app-id' => self::$appId,
                '--index' => [self::$index],
                '--quiet' => true,
            ]
        ));

        $this->assertTokenExists();

        static::$application->run(new ArrayInput(
            [
                'command' => 'apisearch:delete-token',
                'uuid' => $this->token,
                'app-id' => self::$appId,
                '--quiet' => true,
            ]
        ));

        $this->assertTokenNotExists();

        static::$application->run(new ArrayInput(
            [
                'command' => 'apisearch:delete-index',
                'app-id' => self::$appId,
                'index' => self::$index,
                '--quiet' => true,
            ]
        ));
    }
}
