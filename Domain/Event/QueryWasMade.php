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

use Apisearch\Model\ItemUUID;
use Apisearch\Model\User;

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
     * @var int
     *
     * Size
     */
    private $size;

    /**
     * @var ItemUUID[]
     *
     * Items UUID
     */
    private $itemsUUID;

    /**
     * @var User|null
     *
     * User
     */
    private $user;

    /**
     * @var string
     *
     * Query serialized
     */
    private $querySerialized;

    /**
     * QueryWasMade constructor.
     *
     * @param string     $queryText
     * @param int        $size
     * @param ItemUUID[] $itemsUUID
     * @param User|null  $user
     * @param string     $querySerialized
     */
    public function __construct(
        string $queryText,
        int $size,
        array $itemsUUID,
        ? User $user,
        string $querySerialized
    ) {
        $this->queryText = $queryText;
        $this->size = $size;
        $this->itemsUUID = $itemsUUID;
        $this->user = $user;
        $this->setNow();
        $this->querySerialized = $querySerialized;
    }

    /**
     * to array payload.
     *
     * @return array
     */
    public function toArrayPayload(): array
    {
        return [
            'q' => $this->queryText,
            'q_empty' => empty($this->queryText),
            'q_length' => strlen($this->queryText),
            'size' => $this->size,
            'item_uuid' => array_values(
                array_map(function (ItemUUID $itemUUID) {
                    return $itemUUID->composeUUID();
                }, $this->itemsUUID)
            ),
            'result_length' => count($this->itemsUUID),
            'user' => ($this->user instanceof User)
                ? $this->user->toArray()
                : null,
            'query_serialized' => $this->querySerialized,
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
            $arrayPayload['q'],
            $arrayPayload['size'],
            array_map(function (string $composedItemUUID) {
                return ItemUUID::createByComposedUUID($composedItemUUID);
            }, $arrayPayload['item_uuid']),
            isset($arrayPayload['user'])
                ? User::createFromArray($arrayPayload['user'])
                : null,
            $arrayPayload['query_serialized'],
        ];
    }
}
