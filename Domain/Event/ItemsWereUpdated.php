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
     * to array payload.
     *
     * @return array
     */
    public function toArrayPayload(): array
    {
        return [
            'filters' => \json_encode(array_map(function (Filter $filter) {
                return $filter->toArray();
            }, $this->appliedFilters)),
            'changes' => \json_encode($this->changes->toArray()),
        ];
    }

    /**
     * To payload.
     *
     * @param array $arrayPayload
     *
     * @return array
     */
    public static function fromArrayPayload(array $arrayPayload): array
    {
        return [
            array_values(
                array_map(function (array $filter) {
                    return Filter::createFromArray($filter);
                }, (\json_decode($arrayPayload['filters'], true)))
            ),
            Changes::createFromArray(\json_decode($arrayPayload['changes'], true)),
        ];
    }
}
