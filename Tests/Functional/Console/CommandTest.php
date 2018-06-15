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

use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Query\Query;
use Apisearch\Server\Tests\Functional\HttpFunctionalTest;
use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;
use Exception;

/**
 * File header placeholder.
 */
abstract class CommandTest extends HttpFunctionalTest
{
    /**
     * @var string
     *
     * Custom token
     */
    protected $token = '7db56b13-3a4f-d2d3-fd37-a702aca33225';

    /**
     * Save events.
     *
     * @return bool
     */
    protected static function saveEvents(): bool
    {
        return false;
    }

    /**
     * Save logs.
     *
     * @return bool
     */
    protected static function saveLogs(): bool
    {
        return false;
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        try {
            static::deleteIndex(self::$appId, self::$index);
        } catch (ResourceNotAvailableException $e) {
            // Silent pass
        }
    }

    /**
     * Assert index exists.
     */
    protected function assertExistsIndex()
    {
        $this->assertTrue(
            $this->checkIndex()
        );
    }

    /**
     * Assert index not exists.
     */
    protected function assertNotExistsIndex()
    {
        $this->assertFalse(
            $this->checkIndex()
        );
    }

    /**
     * Assert index exists.
     */
    protected function assertExistsEventsIndex()
    {
        $this->queryEvents(
            Query::createMatchAll()
        );
    }

    /**
     * Assert index not exists.
     */
    protected function assertNotExistsEventsIndex()
    {
        try {
            $this->assertExistsEventsIndex();
            $this->fail('Events index should not exist');
        } catch (Exception $e) {
            // OK
        }
    }

    /**
     * Assert index exists.
     */
    protected function assertExistsLogsIndex()
    {
        $this->queryLogs(
            Query::createMatchAll()
        );
    }

    /**
     * Assert index not exists.
     */
    protected function assertNotExistsLogsIndex()
    {
        try {
            $this->assertExistsLogsIndex();
            $this->fail('Logs index should not exist');
        } catch (Exception $e) {
            // OK
        }
    }

    /**
     * Assert token is valid.
     *
     * @param string|null $token
     */
    protected function assertTokenExists(?string $token = null)
    {
        $this->assertTrue(
            $this->checkIndex(
                null,
                null,
                new Token(TokenUUID::createById($token ?? $this->token), self::$appId)
            )
        );
    }

    /**
     * Assert token does not exist.
     *
     * @param string|null $token
     */
    protected function assertTokenNotExists(?string $token = null)
    {
        try {
            $this->assertTokenExists($token);
            $this->fail('Token should not exist');
        } catch (Exception $e) {
            // OK
        }
    }
}
