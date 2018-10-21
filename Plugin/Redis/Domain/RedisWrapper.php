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

namespace Apisearch\Plugin\Redis\Domain;

use Redis;
use RedisCluster;

/**
 * Class RedisWrapper.
 */
class RedisWrapper
{
    /**
     * @var Redis|RedisCluster
     *
     * redis client
     */
    private $redisClient;

    /**
     * @var RedisFactory
     *
     * Redis Factory
     */
    private $redisFactory;

    /**
     * @var RedisConfig
     *
     * Redis config
     */
    private $redisConfig;

    /**
     * RedisWrapper constructor.
     *
     * @param RedisFactory $redisFactory
     * @param RedisConfig  $redisConfig
     */
    public function __construct(
        RedisFactory $redisFactory,
        RedisConfig $redisConfig
    ) {
        $this->redisFactory = $redisFactory;
        $this->redisConfig = $redisConfig;
    }

    /**
     * Get client.
     *
     * @return Redis|RedisCluster
     */
    public function getClient()
    {
        if (is_null($this->redisClient)) {
            $this->redisClient = $this
                ->redisFactory
                ->create($this->redisConfig);
        }

        return $this->redisClient;
    }
}
