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

namespace Apisearch\Plugin\RedisStorage\Tests\Functional;

use Apisearch\Exception\InvalidTokenException;
use Apisearch\Model\AppUUID;
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;
use Apisearch\Query\Query;

/**
 * Class SimpleTokensWorkflowTest.
 */
class SimpleTokensWorkflowTest extends RedisFunctionalTest
{
    /**
     * Test simple workflow.
     */
    public function testSimpleWorkflow()
    {
        $newToken = new Token(TokenUUID::createById('new_token'), AppUUID::createById(self::$appId));
        $this->addToken($newToken);

        $this->assertCount(
            5,
            $this->query(
                Query::createMatchAll(),
                null,
                null,
                $newToken
            )->getItems()
        );

        try {
            $this->query(
                Query::createMatchAll(),
                null,
                null,
                new Token(TokenUUID::createById('non-really-existing'), AppUUID::createById(self::$appId))
            );

            $this->fail('Non invalid token should throw exception');
        } catch (InvalidTokenException $e) {
            // Pass
        }
    }
}
