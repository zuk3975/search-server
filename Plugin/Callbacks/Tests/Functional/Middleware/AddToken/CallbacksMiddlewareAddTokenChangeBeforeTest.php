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
use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;

/**
 * Class CallbacksMiddlewareAddTokenChangeBeforeTest.
 */
class CallbacksMiddlewareAddTokenChangeBeforeTest extends EndpointsFunctionalTest
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
                    'command' => 'AddToken',
                    'endpoint' => '/plugin/endpoints/change_token?'.static::getUrlQuery(),
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
        $this->addToken(new Token(TokenUUID::createById('lalaland'), self::$appId));
        $this->assertInstanceOf(
            Token::class,
            $this->getTokens()['lalaland000']
        );
    }
}
