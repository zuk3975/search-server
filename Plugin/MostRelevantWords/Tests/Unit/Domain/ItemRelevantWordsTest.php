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

namespace Apisearch\Plugin\MostRelevantWords\Tests\Unit\Domain;

use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Plugin\MostRelevantWords\Domain\ItemRelevantWords;
use PHPUnit\Framework\TestCase;

/**
 * Class ItemRelevantWordsTest.
 */
class ItemRelevantWordsTest extends TestCase
{
    /**
     * @var string
     *
     * Text
     */
    private $text1 = 'The house of my house is your house. My wife has been
        married with your wife, in my house. If my wife in this house is always
        that good, then the house will be good within my wife. And a horse.';

    private $text2 = 'yes, you are this kind of person that yes, will always be
        a   simple kind of person , -, kind, person. Yes.';

    /**
     * Simple test.
     */
    public function testSimple()
    {
        $itemRelevantWords = new ItemRelevantWords([
            'field1' => [
                'maximum_words' => 3,
                'minimum_frequency' => 2,
                'minimum_length' => 4,
            ],
        ]);

        $item = Item::create(
            ItemUUID::createByComposedUUID('1~item'),
            [],
            [],
            [
                'field1' => $this->text1,
                'field2' => $this->text2,
            ]
        );

        $itemRelevantWords->reduceItemSearchableFields($item);
        $this->assertEquals($item->getSearchableMetadata()['field2'], $this->text2);
        $field1 = $item->getSearchableMetadata()['field1'];
        $this->assertEquals(
            'house house your house wife your wife house wife house house wife',
            $field1
        );
    }

    /**
     * Simple multiple fields.
     */
    public function testMultipleFields()
    {
        $itemRelevantWords = new ItemRelevantWords([
            'field1' => [
                'maximum_words' => 5,
                'minimum_frequency' => 3,
                'minimum_length' => 1,
            ],
            'field2' => [
                'maximum_words' => 4,
                'minimum_frequency' => 3,
                'minimum_length' => 2,
            ],
        ]);

        $item = Item::create(
            ItemUUID::createByComposedUUID('1~item'),
            [],
            [],
            [
                'field1' => $this->text1,
                'field2' => $this->text2,
            ]
        );

        $itemRelevantWords->reduceItemSearchableFields($item);
        $field1 = $item->getSearchableMetadata()['field1'];
        $this->assertEquals(
            'house my house house my wife wife my house my wife house house my wife',
            $field1
        );
        $field2 = $item->getSearchableMetadata()['field2'];
        $this->assertEquals(
            'yes kind person yes kind person kind person yes',
            $field2
        );
    }
}
