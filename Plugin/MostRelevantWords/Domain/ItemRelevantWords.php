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

namespace Apisearch\Plugin\MostRelevantWords\Domain;

use Apisearch\Model\Item;

/**
 * Class ItemRelevantWords.
 */
class ItemRelevantWords
{
    /**
     * @var array
     *
     * Fields
     */
    private $fields;

    /**
     * ItemRelevantWords constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Given an Item, reduce the body of the searchable metadata fields.
     *
     * @param Item $item
     */
    public function reduceItemSearchableFields(Item $item)
    {
        $searchableMetadata = $item->getSearchableMetadata();
        foreach ($this->fields as $field => $config) {
            if (!isset($searchableMetadata[$field])) {
                continue;
            }

            $searchableMetadata[$field] = $this->reduceValue(
                $searchableMetadata[$field],
                $config
            );
        }

        $item->setSearchableMetadata($searchableMetadata);
    }

    /**
     * Given a text value, or an array of text values, apply given configuration.
     *
     * @param string|string[] $value
     * @param array           $config
     *
     * @return string|string[]
     */
    private function reduceValue(
        $value,
        array $config
    ) {
        if (is_string($value)) {
            return $this->reduceStringValue(
                $value,
                $config
            );
        }

        return array_map(function (string $value) use ($config) {
            return $this->reduceStringValue(
                $value,
                $config
            );
        }, $value);
    }

    /**
     * Given a text value, apply given configuration.
     *
     * @param string $value
     * @param array  $config
     *
     * @return string
     */
    private function reduceStringValue(
        string $value,
        array $config
    ) {
        $value = preg_replace('!\s+!', ' ', strtolower($value));
        $value = preg_replace('/[^\w]+/i', ' ', $value);
        $words = explode(' ', $value);
        $words = array_map('trim', $words);
        $wordsCountedAll = array_count_values($words);
        $wordsCounted = array_filter($wordsCountedAll, function (int $times, string $word) use ($config) {
            return
                !empty($word) &&
                strlen($word) >= $config['minimum_length'] &&
                $times >= $config['minimum_frequency'];
        }, ARRAY_FILTER_USE_BOTH);
        arsort($wordsCounted);
        $wordsCounted = array_slice($wordsCounted, 0, $config['maximum_words']);
        $wordsToKeep = array_intersect($words, array_keys($wordsCounted));

        return implode(' ', $wordsToKeep);
    }
}
