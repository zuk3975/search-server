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

namespace Apisearch\Plugin\Callbacks\Tests\Functional\Middleware\Query;

use Apisearch\Plugin\Callbacks\Tests\Functional\EndpointsFunctionalTest;
use Apisearch\Query\Query;
use Apisearch\Query\SortBy;

/**
 * Class CallbacksMiddlewareQueryChangeBeforeTest.
 */
class CallbacksMiddlewareQueryChangeBeforeTest extends EndpointsFunctionalTest
{
    /**
     * Get callbacks configuration.
     *
     * @return array
     */
    protected static function getCallbacksConfiguration(): array
    {
        return [
            'http_client_adapter' => 'http_test',
            'callbacks' => [
                'my_query_callback' => [
                    'command' => 'Query',
                    'endpoint' => '/plugin/endpoints/change_query_size?'.static::getUrlQuery(),
                    'method' => 'GET',
                    'moment' => 'before',
                ],
                'my_query_callback_2' => [
                    'command' => 'Query',
                    'endpoint' => '/plugin/endpoints/change_query_size?'.static::getUrlQuery(),
                    'method' => 'GET',
                    'moment' => 'before',
                ],
            ],
        ];
    }

    /**
     * Test something.
     */
    public function testSomething()
    {
        $this->assertCount(3,
            $this->query(Query::createMatchAll()->sortBy(SortBy::create()->byValue(SortBy::AL_TUN_TUN)))->getItems()
        );
    }
}
