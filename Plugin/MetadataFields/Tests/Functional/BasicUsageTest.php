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

namespace Apisearch\Plugin\MetadataFields\Tests\Functional;

use Apisearch\Model\ItemUUID;
use Apisearch\Query\Query;

/**
 * Class BasicUsageTest.
 */
class BasicUsageTest extends MetadataFieldsFunctionalTest
{
    /**
     * Basic usage.
     *
     * @group metadatafields
     * @group plugin
     */
    public function testBasicUsage()
    {
        $inMemoryRepository = self::get('apisearch_plugin.metadata_fields.in_memory_repository');
        $this->assertCount(5, $inMemoryRepository->getStorage());
        $item = $this->query(Query::createMatchAll())->getFirstItem();
        $this->assertTrue(isset($item->getMetadata()['array_of_arrays']));

        $this->deleteItems([
            ItemUUID::createByComposedUUID('4~bike'),
            ItemUUID::createByComposedUUID('4~bike'),
            ItemUUID::createByComposedUUID('3~book'),
        ]);
        $this->assertCount(3, $inMemoryRepository->getStorage());
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
        $configuration['apisearch_plugin_metadata_fields']['repository_service'] = 'apisearch_plugin.metadata_fields.in_memory_repository';

        return $configuration;
    }
}
