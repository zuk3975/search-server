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

namespace Apisearch\Server\Redis;

use Redis;
use RedisCluster;
use RSQueue\RedisFactory;

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
     * RedisWrapper constructor.
     *
     * @param RedisFactory $redisFactory
     */
    public function __construct(RedisFactory $redisFactory)
    {
        $this->redisFactory = $redisFactory;
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
                ->create();
        }

        return $this->redisClient;
    }
}
