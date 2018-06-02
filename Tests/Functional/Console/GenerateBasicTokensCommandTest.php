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

use Apisearch\Exception\InvalidTokenException;
use Apisearch\Query\Query;
use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;

/**
 * Class GenerateBasicTokensCommandTest.
 */
class GenerateBasicTokensCommandTest extends CommandTest
{
    /**
     * Test token creation.
     */
    public function testTokenCreation()
    {
        static::runCommand([
            'command' => 'apisearch-server:create-index',
            'app-id' => self::$appId,
            'index' => self::$index,
        ]);

        $output = static::runCommand([
            'command' => 'apisearch-server:generate-basic-tokens',
            'app-id' => static::$appId,
        ]);

        preg_match('~UUID\s*(.*?)\s*generated for admin~', $output, $matches);
        $uuidAdmin = $matches[1];
        preg_match('~UUID\s*(.*?)\s*generated for query~', $output, $matches);
        $uuidQuery = $matches[1];
        preg_match('~UUID\s*(.*?)\s*generated for events~', $output, $matches);
        $uuidEvents = $matches[1];
        preg_match('~UUID\s*(.*?)\s*generated for interaction~', $output, $matches);
        $uuidInteractions = $matches[1];

        $adminToken = new Token(TokenUUID::createById($uuidAdmin), self::$appId);
        $queryToken = new Token(TokenUUID::createById($uuidQuery), self::$appId);
        $eventsToken = new Token(TokenUUID::createById($uuidEvents), self::$appId);
        // $interactionsToken = new Token(TokenUUID::createById($uuidInteractions), self::$appId);

        $this->query(Query::createMatchAll(), null, null, $adminToken);
        $this->query(Query::createMatchAll(), null, null, $queryToken);
        $this->queryEvents(Query::createMatchAll(), null, null, null, null, $adminToken);
        $this->queryEvents(Query::createMatchAll(), null, null, null, null, $eventsToken);
        $this->queryLogs(Query::createMatchAll(), null, null, null, null, $adminToken);
        // $this->addInteraction('1234', '1~product', 10, self::$appId, $adminToken);
        // $this->addInteraction('1234', '1~product', 10, self::$appId, $interactionsToken);

        try {
            $this->query(Query::createMatchAll(), null, null, $eventsToken);
            $this->fail('Query endpoint should not be accessible with an events token');
        } catch (InvalidTokenException $e) {
            // Silent pass
        }

        try {
            $this->queryEvents(Query::createMatchAll(), null, null, null, null, $queryToken);
            $this->fail('Events endpoint should not be accessible with an query token');
        } catch (InvalidTokenException $e) {
            // Silent pass
        }
    }
}
