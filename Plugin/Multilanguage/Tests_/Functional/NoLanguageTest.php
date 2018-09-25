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
 * Class NoLanguageTest.
 */
class NoLanguageTest extends MultilanguageFunctionalTest
{
    /**
     * Basic usage.
     */
    public function testBasicUsage()
    {
        $this->assertFalse(
            $this->checkIndex(self::$appId, self::$index.'-plugin-language-es')
        );

        $this->assertFalse(
            $this->checkIndex(self::$appId, self::$index.'-plugin-language-ca')
        );

        $this->assertFalse(
            $this->checkIndex(self::$appId, self::$index.'-plugin-language-en')
        );

        $this->assertTrue(
            $this->checkIndex(self::$appId, self::$index.'-plugin-language-xx')
        );

        $this->assertCount(3, $this->query(Query::createMatchAll())->getItems());
        $this->assertCount(
            1,
            $this->query(Query::create('per'))->getItems()
        );
    }

    /**
     * Get items file path.
     *
     * @return string
     */
    public static function getItemsFilePath(): string
    {
        return __DIR__.'/items_no_language.yml';
    }
}
