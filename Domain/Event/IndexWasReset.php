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

use Apisearch\Model\IndexUUID;

/**
 * Class IndexWasReset.
 */
class IndexWasReset extends DomainEvent
{
    /**
     * @var IndexUUID
     *
     * Index UUID
     */
    private $indexUUID;

    /**
     * IndexWasConfigured constructor.
     *
     * @param IndexUUID $indexUUID
     */
    public function __construct(IndexUUID $indexUUID)
    {
        $this->indexUUID = $indexUUID;
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
            'index_uuid' => $this
                ->indexUUID
                ->composeUUID(),
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
            IndexUUID::createById($arrayPayload['index_uuid']),
        ];
    }
}
