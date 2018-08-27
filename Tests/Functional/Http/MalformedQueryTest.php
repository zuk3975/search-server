<?php
/**
 * File header placeholder
 */

namespace Apisearch\Server\Tests\Functional\Http;

use Apisearch\Server\Tests\Functional\CurlFunctionalTest;

/**
 * Class MalformedQueryTest
 */
class MalformedQueryTest extends CurlFunctionalTest
{
    /**
     * Test malformed query
     *
     * @expectedException \Apisearch\Exception\InvalidFormatException
     */
    public function testMalformedQuery()
    {
        self::makeCurl(
            'v1-query',
            self::$appId,
            self::$index,
            null,
            '{"query":{"q":"","aggregations":{"undefined":{"field":"indexed_metadata.anon"}},"size":1000}}'
        );
    }
}