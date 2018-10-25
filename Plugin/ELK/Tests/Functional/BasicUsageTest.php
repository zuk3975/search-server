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

namespace Apisearch\Plugin\ELK\Tests\Functional;

use Apisearch\Query\Query;

/**
 * Class BasicUsageTest.
 */
class BasicUsageTest extends ELKFunctionalTest
{
    /**
     * Basic usage.
     */
    public function testBasicUsage()
    {
        $redis = new \Redis();
        $redis->connect('apisearch.redis');
        $redis->del('apisearch_test.elk');

        $this->query(Query::createMatchAll());
        $this->assertEquals(
            1,
            $redis->lLen('apisearch_test.elk')
        );

        $redis->del('apisearch_test.elk');
        $this->deleteIndex();
        $this->createIndex();
        $this->indexTestingItems();
        $this->query(Query::createMatchAll());
        $this->assertEquals(
            2,
            $redis->lLen('apisearch_test.elk')
        );
    }

    /**
     * Decorate configuration.
     *
     * @param array $configuration
     *
     * @return array
     */
    protected static function decorateConfiguration(array $configuration): array
    {
        $configuration = parent::decorateConfiguration($configuration);
        $configuration['apisearch_plugin_elk'] = [
            'host' => 'apisearch.redis',
            'port' => 6380,
            'key' => 'apisearch_test.elk',
        ];

        return $configuration;
    }
}
