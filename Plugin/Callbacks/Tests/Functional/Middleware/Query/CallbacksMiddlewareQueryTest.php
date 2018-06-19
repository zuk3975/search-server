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

use Apisearch\Http\Http;
use Apisearch\Plugin\Callbacks\Tests\Functional\EndpointsFunctionalTest;
use Apisearch\Query\Query;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CallbacksMiddlewareQueryTest.
 */
class CallbacksMiddlewareQueryTest extends EndpointsFunctionalTest
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
                'my_query_callback_before' => [
                    'command' => 'Query',
                    'endpoint' => '/plugin/endpoints/empty_endpoint?'.static::getUrlQuery(),
                    'method' => 'GET',
                    'moment' => 'before',
                ],
                'my_query_callback_after' => [
                    'command' => 'Query',
                    'endpoint' => '/plugin/endpoints/empty_endpoint?'.static::getUrlQuery(),
                    'method' => 'GET',
                    'moment' => 'after',
                ],
            ],
        ];
    }

    /**
     * Test something.
     */
    public function testSomething()
    {
        $this->assertCount(5,
            $this->query(Query::createMatchAll())->getItems()
        );

        $controllerRegister = $this->getRegister();
        $this->assertEquals(
            Request::METHOD_GET,
            $controllerRegister[0]['method']
        );
        $this->assertEquals(
            self::$appId,
            $controllerRegister[0]['query']->get(Http::APP_ID_FIELD)
        );
        $this->assertEquals(
            self::$index,
            $controllerRegister[0]['query']->get(Http::INDEX_FIELD)
        );
        $this->assertEquals(
            self::$godToken,
            $controllerRegister[0]['query']->get(Http::TOKEN_FIELD)->getTokenUUID()->composeUUID()
        );
    }
}
