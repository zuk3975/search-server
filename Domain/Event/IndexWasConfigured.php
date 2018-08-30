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

use Apisearch\Config\Config;
use Apisearch\Model\IndexUUID;

/**
 * Class IndexWasConfigured.
 */
class IndexWasConfigured extends DomainEvent
{
    /**
     * @var IndexUUID
     *
     * Index UUID
     */
    private $indexUUID;

    /**
     * @var Config
     *
     * Config
     */
    private $config;

    /**
     * IndexWasConfigured constructor.
     *
     * @param IndexUUID $indexUUID
     * @param Config    $config
     */
    public function __construct(
        IndexUUID $indexUUID,
        Config $config
    ) {
        $this->config = $config;
        $this->indexUUID = $indexUUID;
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
            'index_uuid' => $this
                ->indexUUID
                ->toArray(),
            'config' => $this
                ->config
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
        return [
            IndexUUID::createFromArray($data['index_uuid']),
            Config::createFromArray($data['config']),
        ];
    }
}
