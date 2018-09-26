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

namespace Apisearch\Server\Tests\Functional\Domain\Event;

use Apisearch\Query\Query;
use Apisearch\Server\Domain\Event\CollectInMemoryDomainEventSubscriber;
use Apisearch\Server\Tests\Functional\ServiceFunctionalTest;

/**
 * Class DomainEventSubscriptionTest.
 */
class DomainEventSubscriptionTest extends ServiceFunctionalTest
{
    /**
     * Test normal behavior of event subscription.
     */
    public function testBasicBehavior()
    {
        $subscriber = new CollectInMemoryDomainEventSubscriber();
        $this
            ->get('apisearch_server.event_publisher')
            ->subscribe($subscriber);

        $this->query(Query::createMatchAll());
        $this->assertCount(1, $subscriber->getEvents());
    }

    /**
     * Test normal behavior of event subscription using tagged service.
     */
    public function testBasicBehaviorWithTaggedService()
    {
        $this->assertCount(6, $this
            ->get('apisearch_server.test.in_memory_domain_event_collector')
            ->getEvents()
        );
    }
}
