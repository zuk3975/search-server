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

namespace Apisearch\Server\Domain\QueryHandler;

use Apisearch\Server\Domain\Query\CheckHealth;
use Apisearch\Server\Redis\RedisWrapper;
use Elastica\Client;

/**
 * Class CheckHealthHandler.
 */
class CheckHealthHandler
{
    /**
     * @var Client
     *
     * Elasticsearch Client
     */
    protected $client;

    /**
     * @var RedisWrapper
     *
     * Redis wrapper
     */
    protected $redisWrapper;

    /**
     * QueryHandler constructor.
     *
     * @param Client       $client
     * @param RedisWrapper $redisWrapper
     */
    public function __construct(
        Client $client,
        RedisWrapper $redisWrapper
    ) {
        $this->client = $client;
        $this->redisWrapper = $redisWrapper;
    }

    /**
     * Check the cluster.
     *
     * @param CheckHealth $checkHealth
     *
     * @return array
     */
    public function handle(CheckHealth $checkHealth): array
    {
        return [
            'status' => [
                'elasticsearch' => $this->getElasticsearchClusterStatus(),
                'redis' => $this->getRedisStatus(),
            ],
            'process' => [
                'memory_used' => memory_get_usage(false),
            ],
        ];
    }

    /**
     * Get redis status.
     *
     * @return bool
     */
    private function getRedisStatus(): bool
    {
        try {
            $pong = $this
                ->redisWrapper
                ->getClient()
                ->ping();

            return '+PONG' === $pong;
        } catch (\RedisException $e) {
            // Silent pass
        }

        return false;
    }

    /**
     * Get elasticsearch cluster status.
     *
     * @return string
     */
    private function getElasticsearchClusterStatus(): string
    {
        return $this
            ->client
            ->getCluster()
            ->getHealth()
            ->getStatus();
    }
}
