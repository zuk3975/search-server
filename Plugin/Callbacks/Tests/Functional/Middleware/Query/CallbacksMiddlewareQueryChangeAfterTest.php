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

/**
 * Class CallbacksMiddlewareQueryChangeAfterTest.
 */
class CallbacksMiddlewareQueryChangeAfterTest extends EndpointsFunctionalTest
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
                'my_query_callback_after' => [
                    'command' => 'Query',
                    'endpoint' => '/plugin/endpoints/change_query_result?'.static::getUrlQuery(),
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
        $result = $this->query(Query::createMatchAll());
        $this->assertCount(5,
            $result->getItems()
        );

        foreach ($result->getItems() as $item) {
            $this->assertTrue(
                $item->get('modified')
            );
        }
    }
}
