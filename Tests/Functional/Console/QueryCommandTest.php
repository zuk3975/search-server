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

namespace Apisearch\Server\Tests\Functional\Console;

/**
 * Class QueryCommandTest.
 */
class QueryCommandTest extends CommandTest
{
    /**
     * Test create index command.
     */
    public function testCreateIndexCommand()
    {
        $this->assertNotExistsIndex();

        static::runCommand([
            'command' => 'apisearch-server:create-index',
            'app-id' => self::$appId,
            'index' => self::$index,
        ]);

        static::runCommand([
            'command' => 'apisearch-server:import-index',
            'app-id' => self::$appId,
            'index' => self::$index,
            'file' => __DIR__.'/data.as',
        ]);

        $content = static::runCommand([
            'command' => 'apisearch-server:query',
            'app-id' => self::$appId,
            'index' => self::$index,
        ]);

        $this->assertTrue(
            strpos($content, '* / 1 / 10') !== false
        );

        $this->assertTrue(
            strpos($content, ' 28') !== false
        );

        $this->assertTrue(
            strpos($content, 'siege_vol_1_1~book') !== false
        );

        $content2 = static::runCommand([
            'command' => 'apisearch-server:query',
            'app-id' => self::$appId,
            'index' => self::$index,
            'query' => 'Robert Grayson'
        ]);

        $this->assertTrue(
            strpos($content2, 'Robert Grayson / 1 / 10') !== false
        );

        $this->assertTrue(
            strpos($content2, ' 4') !== false
        );

        $this->assertfalse(
            strpos($content2, 'siege_vol_1_1~book')
        );

        $this->assertTrue(
            strpos($content2, 'marvel_boy:_the_uranian_vol_1_3~book') !== false
        );

        $content3 = static::runCommand([
            'command' => 'apisearch-server:query',
            'app-id' => self::$appId,
            'index' => self::$index,
            'query' => 'Robert Grayson',
            '--page' => 1,
            '--size' => 2
        ]);

        $this->assertTrue(
            strpos($content3, 'Robert Grayson / 1 / 2') !== false
        );

        $this->assertTrue(
            strpos($content3, ' 4') !== false
        );

        $this->assertfalse(
            strpos($content3, 'siege_vol_1_1~book')
        );

        $this->assertTrue(
            strpos($content3, 'marvel_boy:_the_uranian_vol_1_1~book') !== false
        );
    }
}
