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

namespace Apisearch\Server\Tests\Functional\Http;

use Apisearch\Server\Tests\Functional\HttpFunctionalTest;
use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CreateIndexTest.
 */
class CreateIndexTest extends HttpFunctionalTest
{
    /**
     * Test check health with different tokens.
     */
    public function testCreateIndex(): void
    {
        self::loadEnv();
        $token = $_ENV['APISEARCH_GOD_TOKEN'];

        $this->deleteIndex(
            self::$appId,
            self::$index,
            new Token(TokenUUID::createById($token), self::$appId)
        );

        $client = $this->createClient();
        $testRoute = $this->get('router')->generate('search_server_api_create_index', [
            'token' => $token,
            'app_id' => self::$appId,
            'index' => self::$index,
        ]);

        $client->request(
            'post',
            $testRoute
        );

        $response = $client->getResponse();
        $this->assertEquals(
            JsonResponse::HTTP_CREATED,
            $response->getStatusCode()
        );

        $anotherClient = $this->createClient();
        $anotherClient->request(
            'post',
            $testRoute
        );

        $anotherResponse = $anotherClient->getResponse();
        $this->assertEquals(
            JsonResponse::HTTP_CONFLICT,
            $anotherResponse->getStatusCode()
        );
    }
}
