<?php

/*
 * This file is part of the SearchBundle for Symfony2.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Mmoreram\SearchBundle\Tests\Functional\Core;

use Mmoreram\SearchBundle\Model\Brand;
use Mmoreram\SearchBundle\Model\Category;
use Mmoreram\SearchBundle\Model\Manufacturer;
use Mmoreram\SearchBundle\Model\Product;
use Mmoreram\SearchBundle\Query\Query;

/**
 * Class SearchTest.
 */
class SearchTest extends ElasticaSearchRepositoryTest
{
    /**
     * Test get match all.
     */
    public function testMatchAll()
    {
        $repository = static::$repository;
        $result = $repository->query(Query::createMatchAll());
        $this->assertEquals(
            count($result->getProducts()),
            $this->get('search_bundle.elastica_wrapper')->getType(self::$key, Product::TYPE)->count()
        );
        $this->assertEquals(
            count($result->getCategories()),
            $this->get('search_bundle.elastica_wrapper')->getType(self::$key, Category::TYPE)->count()
        );
        $this->assertEquals(
            count($result->getManufacturers()),
            $this->get('search_bundle.elastica_wrapper')->getType(self::$key, Manufacturer::TYPE)->count()
        );
        $this->assertEquals(
            count($result->getBrands()),
            $this->get('search_bundle.elastica_wrapper')->getType(self::$key, Brand::TYPE)->count()
        );
    }

    /**
     * Test basic search.
     */
    public function testBasicSearch()
    {
        $repository = static::$repository;

        $result = $repository->query(Query::create('adidas'));
        $this->assertNTypeElementId($result, Product::TYPE, 0, '1');
        $this->assertNTypeElementId($result, Brand::TYPE, 0, '1');
        $this->assertNTypeElementId($result, Manufacturer::TYPE, 0, '1');
    }
}