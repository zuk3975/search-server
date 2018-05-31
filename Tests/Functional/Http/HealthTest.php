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

namespace Apisearch\Server\Tests\Functional\Http;

use Apisearch\Server\Tests\Functional\HttpFunctionalTest;

/**
 * Class HealthTest.
 */
class HealthTest extends HttpFunctionalTest
{
    /**
     * Test check health with different tokens.
     *
     * @param string $token
     * @param int    $responseCode
     *
     * @dataProvider dataCheckHealth
     *
     * @group ht
     */
    public function testCheckHealth(
        string $token,
        int $responseCode
    ) {
        $client = $this->createClient();
        $testRoute = static::get('router')->generate('search_server_api_check_health', [
            'token' => $token,
        ]);

        $client->request(
            'get',
            $testRoute
        );

        $response = $client->getResponse();
        $this->assertEquals(
            $responseCode,
            $response->getStatusCode()
        );

        if (200 === $responseCode) {
            $this->assertEquals(
                [
                    'status' => [
                        'elasticsearch' => 'green',
                        'redis' => true,
                    ],
                ],
                json_decode($response->getContent(), true)
            );
        }
    }

    /**
     * Data for check health testing.
     *
     * @return array
     */
    public function dataCheckHealth(): array
    {
        return [
            [self::$pingToken, 200],
            [self::$godToken, 200],
            ['non-existing-key', 401],
        ];
    }
}
