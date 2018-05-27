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

use Apisearch\Query\Query;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Class CreateDeleteIndexCommandTest.
 */
class CreateDeleteIndexCommandTest extends CommandTest
{
    /**
     * Test create index command.
     */
    public function testCreateIndexCommand()
    {
        $this->assertNotExistsIndex();

        static::$application->run(new ArrayInput(
            [
                'command' => 'apisearch:create-index',
                'app-id' => self::$appId,
                'index' => self::$index,
                '--quiet' => true,
            ]
        ));

        $this->assertExistsIndex();
        $this->assertNotExistsEventsIndex();
        $this->assertNotExistsLogsIndex();

        static::$application->run(new ArrayInput(
            [
                'command' => 'apisearch:delete-index',
                'app-id' => self::$appId,
                'index' => self::$index,
                '--quiet' => true,
            ]
        ));

        $this->assertNotExistsIndex();
    }

    /**
     * Test create index command with events.
     */
    public function testCreateIndexWithEventsCommand()
    {
        $this->assertNotExistsIndex();
        $this->assertNotExistsEventsIndex();

        static::$application->run(new ArrayInput(
            [
                'command' => 'apisearch:create-index',
                'app-id' => self::$appId,
                'index' => self::$index,
                '--with-events' => true,
                '--quiet' => true,
            ]
        ));

        $this->assertExistsIndex();
        $this->assertExistsEventsIndex();
        $this->assertNotExistsLogsIndex();

        static::$application->run(new ArrayInput(
            [
                'command' => 'apisearch:delete-index',
                'app-id' => self::$appId,
                'index' => self::$index,
                '--with-events' => true,
                '--quiet' => true,
            ]
        ));

        $this->assertNotExistsIndex();
        $this->assertNotExistsEventsIndex();
    }

    /**
     * Test create index command with logs.
     */
    public function testCreateIndexWithLogsCommand()
    {
        $this->assertNotExistsIndex();
        $this->assertNotExistsLogsIndex();

        static::$application->run(new ArrayInput(
            [
                'command' => 'apisearch:create-index',
                'app-id' => self::$appId,
                'index' => self::$index,
                '--with-logs' => true,
                '--quiet' => true,
            ]
        ));

        $this->assertExistsIndex();
        $this->assertNotExistsEventsIndex();
        $this->assertExistsLogsIndex();

        static::$application->run(new ArrayInput(
            [
                'command' => 'apisearch:delete-index',
                'app-id' => self::$appId,
                'index' => self::$index,
                '--with-logs' => true,
                '--quiet' => true,
            ]
        ));

        $this->assertNotExistsIndex();
        $this->assertNotExistsLogsIndex();
    }

    /**
     * Test create all indices command.
     */
    public function testCreateAllIndicesCommand()
    {
        $this->assertNotExistsIndex();
        $this->assertNotExistsEventsIndex();
        $this->assertNotExistsLogsIndex();

        static::$application->run(new ArrayInput(
            [
                'command' => 'apisearch:create-index',
                'app-id' => self::$appId,
                'index' => self::$index,
                '--with-events' => true,
                '--with-logs' => true,
                '--quiet' => true,
            ]
        ));

        $this->assertExistsIndex();
        $this->assertExistsEventsIndex();
        $this->assertExistsLogsIndex();

        static::$application->run(new ArrayInput(
            [
                'command' => 'apisearch:delete-index',
                'app-id' => self::$appId,
                'index' => self::$index,
                '--with-events' => true,
                '--with-logs' => true,
                '--quiet' => true,
            ]
        ));

        $this->assertNotExistsIndex();
        $this->assertNotExistsEventsIndex();
        $this->assertNotExistsLogsIndex();
    }

    /**
     * Test create index with synonyms.
     */
    public function testCreateIndexCommandWithSynonyms()
    {
        $this->assertNotExistsIndex();
        static::$application->run(new ArrayInput(
            [
                'command' => 'apisearch:create-index',
                'app-id' => self::$appId,
                'index' => self::$index,
                '--synonym' => [
                    'alfaguarra, percebeiro, engonga',
                ],
                '--quiet' => true,
            ]
        ));
        $this->indexTestingItems(self::$appId, self::$index);

        $result = $this->query(Query::create('alfaguar'));
        $this->assertCount(1, $result->getItems());
        $this->assertEquals(1, $result->getFirstItem()->getId());

        $result = $this->query(Query::create('engong'));
        $this->assertCount(1, $result->getItems());
        $this->assertEquals(1, $result->getFirstItem()->getId());

        $result = $this->query(Query::create('perceb'));
        $this->assertCount(1, $result->getItems());
        $this->assertEquals(1, $result->getFirstItem()->getId());

        static::$application->run(new ArrayInput(
            [
                'command' => 'apisearch:delete-index',
                'app-id' => self::$appId,
                'index' => self::$index,
                '--quiet' => true,
            ]
        ));
        $this->assertNotExistsIndex();
    }
}
