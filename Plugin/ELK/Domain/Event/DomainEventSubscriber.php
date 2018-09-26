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

namespace Apisearch\Plugin\ELK\Domain\Event;

use Apisearch\Plugin\Redis\Domain\RedisWrapper;
use Apisearch\Server\Domain\Event\DomainEventWithRepositoryReference;
use Apisearch\Server\Domain\Event\EventSubscriber;
use Apisearch\Server\Domain\Formatter\TimeFormatBuilder;
use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\RedisHandler;
use Monolog\Logger;
use Monolog\Processor\MemoryPeakUsageProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\WebProcessor;

/**
 * Class DomainEventSubscriber.
 */
class DomainEventSubscriber implements EventSubscriber
{
    /**
     * @var RedisWrapper
     *
     * RedisWrapper
     */
    private $redisWrapper;

    /**
     * @var TimeFormatBuilder
     *
     * Time format builder
     */
    private $timeFormatBuilder;

    /**
     * @var string
     *
     * Key
     */
    private $key;

    /**
     * RedisMetadataRepository constructor.
     *
     * @param RedisWrapper      $redisWrapper
     * @param TimeFormatBuilder $timeFormatBuilder
     * @param string            $key
     */
    public function __construct(
        RedisWrapper $redisWrapper,
        TimeFormatBuilder $timeFormatBuilder,
        string $key
    ) {
        $this->redisWrapper = $redisWrapper;
        $this->timeFormatBuilder = $timeFormatBuilder;
        $this->key = $key;
    }

    /**
     * Subscriber should handle event.
     *
     * @param DomainEventWithRepositoryReference $domainEventWithRepositoryReference
     *
     * @return bool
     */
    public function shouldHandleEvent(DomainEventWithRepositoryReference $domainEventWithRepositoryReference): bool
    {
        return true;
    }

    /**
     * Handle event.
     *
     * @param DomainEventWithRepositoryReference $domainEventWithRepositoryReference
     */
    public function handle(DomainEventWithRepositoryReference $domainEventWithRepositoryReference)
    {
        $redisHandler = new RedisHandler(
            $this
                ->redisWrapper
                ->getClient(),
            $this->key
        );

        $formatter = new LogstashFormatter('apisearch');
        $redisHandler->setFormatter($formatter);
        $logger = new Logger('apisearch_to_logstash', [$redisHandler], [
            new MemoryUsageProcessor(),
            new MemoryPeakUsageProcessor(),
            new WebProcessor(),
        ]);
        $event = $domainEventWithRepositoryReference->getDomainEvent();
        $reducedArray = $event->toReducedArray();
        $reducedArray['occurred_on'] = $this
            ->timeFormatBuilder
            ->formatTimeFromMillisecondsToBasicDateTime(
                $event->occurredOn()
            );

        $logger->info(json_encode([
            'repository_reference' => $domainEventWithRepositoryReference
                ->getRepositoryReference()
                ->compose(),
            'time_cost' => $domainEventWithRepositoryReference->getTimeCost(),
        ] + $reducedArray));
    }
}
