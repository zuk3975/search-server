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

/**
 * Class RedisWrapperFactory.
 */
class RedisWrapperFactory
{
    /**
     * @var RedisWrapper[]
     *
     * Redis wrappers
     */
    private $redisWrappers;

    /**
     * @var RedisFactory
     *
     * Redis factory
     */
    private $redisFactory;

    /**
     * RedisWrapperFactory constructor.
     *
     * @param RedisFactory $redisFactory
     */
    public function __construct(RedisFactory $redisFactory)
    {
        $this->redisFactory = $redisFactory;
    }

    /**
     * Create redis wrapper.
     *
     * @param RedisConfig $redisConfig
     *
     * @return RedisWrapper
     */
    public function create(RedisConfig $redisConfig): RedisWrapper
    {
        $redisConfigSerialized = $redisConfig->serialize();
        if ($this->redisWrappers[$redisConfigSerialized]) {
            return $this->redisWrappers[$redisConfigSerialized];
        }

        $this->redisWrappers[$redisConfigSerialized] = new RedisWrapper(
            $this->redisFactory,
            $redisConfig
        );

        return $this->redisWrappers[$redisConfigSerialized];
    }
}
