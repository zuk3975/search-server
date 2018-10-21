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

use Apisearch\Model\User;
use Apisearch\Query\Filter;
use Apisearch\Query\SortBy;

/**
 * Class QueryWasMade.
 */
class QueryWasMade extends DomainEvent
{
    /**
     * @var string
     *
     * Query text
     */
    private $queryText;

    /**
     * @var Filter[]
     *
     * Applied filters
     */
    private $appliedFilters;

    /**
     * @var SortBy
     *
     * Sort by
     */
    private $sortBy;

    /**
     * @var int
     *
     * Size
     */
    private $size;

    /**
     * @var string[]
     *
     * Result ids
     */
    private $resultIds;

    /**
     * @var User|null
     *
     * User
     */
    private $user;

    /**
     * QueryWasMade constructor.
     *
     * @param string    $queryText
     * @param Filter[]  $appliedFilters
     * @param SortBy    $sortBy
     * @param int       $size
     * @param string[]  $resultIds
     * @param User|null $user
     */
    public function __construct(
        string $queryText,
        array $appliedFilters,
        SortBy $sortBy,
        int $size,
        array $resultIds,
        ? User $user
    ) {
        $this->queryText = $queryText;
        $this->appliedFilters = $appliedFilters;
        $this->sortBy = $sortBy;
        $this->size = $size;
        $this->resultIds = $resultIds;
        $this->user = $user;
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
            'sort_by' => $this
                ->sortBy
                ->toArray(),
        ];
    }

    /**
     * Indexable to array.
     *
     * @return array
     */
    public function indexableToArray(): array
    {
        return [
            'q' => $this->queryText,
            'q_empty' => empty($this->queryText),
            'q_length' => strlen($this->queryText),
            'size' => $this->size,
            'result_ids' => $this->resultIds,
            'result_length' => count($this->resultIds),
            'user' => ($this->user instanceof User)
                ? $this->user->toArray()
                : null,
        ];
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
            $payload['q'],
            array_values(
                array_map(function (array $filter) {
                    return Filter::createFromArray($filter);
                }, ($payload['filters'] ?? []))
            ),
            SortBy::createFromArray($payload['sort_by']),
            $payload['size'],
            $payload['result_ids'] ?? [],
            isset($payload['user'])
                ? User::createFromArray($payload['user'])
                : null,
        ];
    }
}
