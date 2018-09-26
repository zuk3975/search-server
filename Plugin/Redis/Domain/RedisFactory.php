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
 * Class RedisFactory.
 */
class RedisFactory
{
    /**
     * Generate new Predis instance.
     *
     * @param RedisConfig $redisConfig
     *
     * @return Redis|RedisCluster
     */
    public function create(RedisConfig $redisConfig)
    {
        return $redisConfig->isCluster()
            ? $this->createCluster($redisConfig)
            : $this->createSimple($redisConfig);
    }

    /**
     * Create cluster.
     *
     * @param RedisConfig $redisConfig
     *
     * @return RedisCluster
     */
    private function createCluster(RedisConfig $redisConfig): RedisCluster
    {
        return new RedisCluster(null, [$redisConfig->getHost().':'.$redisConfig->getPort()]);
    }

    /**
     * Create single redis.
     *
     * @param RedisConfig $redisConfig
     *
     * @return Redis
     */
    private function createSimple(RedisConfig $redisConfig): Redis
    {
        $redis = new Redis();
        $redis->connect($redisConfig->getHost(), $redisConfig->getPort());
        $redis->setOption(Redis::OPT_READ_TIMEOUT, '-1');
        if (!empty($redisConfig->getDatabase())) {
            $redis->select($redisConfig->getDatabase());
        }

        return $redis;
    }
}
