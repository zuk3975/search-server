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

namespace Apisearch\Server\Tests\Functional\Domain\Repository;

use Apisearch\Config\Config;
use Apisearch\Config\Synonym;
use Apisearch\Query\Query;

/**
 * Class IndexConfigurationTest.
 */
trait IndexConfigurationTest
{
    /**
     * Test index check.
     */
    public function testConfigureIndexWithSynonyms()
    {
        $this->assertCount(0, $this->query(Query::create('Flipencio'))->getItems());
        $this->configureIndex(Config::createEmpty()->addSynonym(Synonym::createByWords(['Alfaguarra', 'Flipencio'])));
        sleep(1);
        $this->indexTestingItems();
        $this->assertCount(1, $this->query(Query::create('Flipencio'))->getItems());
    }
}
