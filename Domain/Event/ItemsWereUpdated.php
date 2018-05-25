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

namespace Apisearch\Server\Domain\Event;

use Apisearch\Model\Changes;
use Apisearch\Query\Filter;

/**
 * Class ItemsWereUpdated.
 */
class ItemsWereUpdated extends DomainEvent
{
    /**
     * @var Filter[]
     *
     * Applied filters
     */
    private $appliedFilters;

    /**
     * @var Changes
     *
     * Changes
     */
    private $changes;

    /**
     * ItemsWasIndexed constructor.
     *
     * @param Filter[] $appliedFilters
     * @param Changes  $changes
     */
    public function __construct(
        array $appliedFilters,
        Changes $changes
    ) {
        $this->appliedFilters = $appliedFilters;
        $this->changes = $changes;
        $this->setNow();
    }

    /**
     * Indexable to array.
     *
     * @return array
     */
    public function readableOnlyToArray(): array
    {
        return [
            'filters' => array_map(function (Filter $filter) {
                return $filter->toArray();
            }, $this->appliedFilters),
            'changes' => $this->changes->toArray(),
        ];
    }

    /**
     * Indexable to array.
     *
     * @return array
     */
    public function indexableToArray(): array
    {
        return [];
    }

    /**
     * To payload.
     *
     * @param string $data
     *
     * @return array
     */
    public static function stringToPayload(string $data): array
    {
        $payload = json_decode($data, true);

        return [
            array_values(
                array_map(function (array $filter) {
                    return Filter::createFromArray($filter);
                }, ($payload['filters'] ?? []))
            ),
            Changes::createFromArray($payload['changes'] ?? []),
        ];
    }
}
