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

namespace Apisearch\Plugin\MostRelevantWords\Tests\Functional;

use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Query\Query;

/**
 * Class BasicUsageTest.
 */
class BasicUsageTest extends MostRelevantWordsFunctionalTest
{
    /**
     * Basic usage.
     */
    public function testBasicUsage()
    {
        $item = Item::create(
            ItemUUID::createByComposedUUID('1~special'),
            [],
            [],
            [
                'field1' => 'Nory was a Catholic because her mother was a 
                Catholic, and Nory’s mother was a Catholic because her father 
                was a Catholic, and her father was a Catholic because his mother 
                was a Catholic, or had been.',
            ]
        );

        static::indexItems([$item]);
        $item = $this->query(Query::createByUUID(ItemUUID::createByComposedUUID('1~special')))->getFirstItem();
        $this->assertEquals(
            'catholic because mother catholic mother catholic because catholic catholic because mother catholic',
            $item->getSearchableMetadata()['field1']
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
        $configuration['apisearch_plugin_most_relevant_words']['fields'] = [
            'field1' => [
                'maximum_words' => 20,
                'minimum_frequency' => 3,
                'minimum_length' => 4,
            ],
        ];

        return $configuration;
    }
}
