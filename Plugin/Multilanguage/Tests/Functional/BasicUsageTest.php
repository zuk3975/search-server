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

use Apisearch\Query\Query;

/**
 * Class BasicUsageTest.
 */
class BasicUsageTest extends MultilanguageFunctionalTest
{
    /**
     * Basic usage.
     */
    public function testBasicUsage()
    {
        usleep(500000);
        $this->assertTrue(
            $this->checkIndex(self::$appId, self::$index.'_plugin_language_es')
        );

        $this->assertTrue(
            $this->checkIndex(self::$appId, self::$index.'_plugin_language_ca')
        );

        $this->assertTrue(
            $this->checkIndex(self::$appId, self::$index.'_plugin_language_en')
        );

        $this->assertCount(3, $this->query(Query::createMatchAll())->getItems());
        $this->assertCount(1, $this->query(Query::createMatchAll()->filterBy('language', 'language', ['ca']))->getItems());
        $this->assertCount(1, $this->query(Query::createMatchAll()->filterBy('language', 'language', ['es']))->getItems());
        $this->assertCount(1, $this->query(Query::createMatchAll()->filterBy('language', 'language', ['en']))->getItems());
        $this->assertCount(2, $this->query(Query::createMatchAll()->filterBy('language', 'language', ['en', 'ca']))->getItems());
        $this->assertCount(3, $this->query(Query::createMatchAll()->filterBy('language', 'language', ['en', 'ca', 'es']))->getItems());

        $this->assertCount(1, $this->query(Query::createMatchAll()->filterBy('language', 'language', ['ca']))->getItems(), self::$appId, self::$index.'_plugin_language_en');
        $this->assertCount(1, $this->query(Query::createMatchAll()->filterBy('language', 'language', ['es']))->getItems(), self::$appId, self::$index.'_plugin_language_en');
        $this->assertCount(1, $this->query(Query::createMatchAll()->filterBy('language', 'language', ['en']))->getItems(), self::$appId, self::$index.'_plugin_language_en');

        $this->assertCount(
            0,
            $this->query(Query::create('per'))->getItems()
        );
    }
}
