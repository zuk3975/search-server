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

namespace Apisearch\Plugin\Multilanguage\Tests\Functional;

use Apisearch\Exception\InvalidTokenException;
use Apisearch\Query\Query;
use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;

/**
 * Class SimpleTokensWorkflowDisabledTest.
 */
class SimpleTokensWorkflowDisabledTest extends RedisFunctionalTest
{
    /**
     * Decorate configuration.
     *
     * @param array $configuration
     *
     * @return array
     */
    protected static function decorateConfiguration(array $configuration): array
    {
        $configuration['apisearch_plugin_redis']['locator_enabled'] = false;

        return $configuration;
    }

    /**
     * Test simple workflow.
     */
    public function testSimpleWorkflow()
    {
        $newToken = new Token(TokenUUID::createById('new_token'), self::$appId);
        $this->addToken($newToken);

        try {
            $this->query(
                Query::createMatchAll(),
                null,
                null,
                new Token(TokenUUID::createById('non-really-existing'), self::$appId)
            );

            $this->fail('Non invalid token should throw exception');
        } catch (InvalidTokenException $e) {
            // Pass
        }
    }
}
