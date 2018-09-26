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

namespace Apisearch\Plugin\RSQueue\Console;

use Apisearch\Server\Domain\EventConsumer\EventConsumer;
use RSQueue\Command\ConsumerCommand;
use RSQueue\Services\Consumer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RSQueueDomainEventsConsumer.
 */
class RSQueueDomainEventsConsumer extends ConsumerCommand
{
    /**
     * @var EventConsumer
     *
     * Event consumer
     */
    private $eventConsumer;

    /**
     * ConsumerCommand constructor.
     *
     * @param Consumer      $consumer
     * @param EventConsumer $eventConsumer
     */
    public function __construct(
        Consumer $consumer,
        EventConsumer $eventConsumer
    ) {
        parent::__construct($consumer);

        $this->eventConsumer = $eventConsumer;
    }

    /**
     * Definition method.
     *
     * All RSQueue commands must implements its own define() method
     * This method will subscribe command to desired queues
     * with their respective methods
     */
    public function define()
    {
        $this->addQueue('events_queue', 'consumeDomainEvent');
    }

    /**
     * Persist domain event.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param array           $data
     */
    protected function consumeDomainEvent(
        InputInterface $input,
        OutputInterface $output,
        array $data
    ) {
        $this
            ->eventConsumer
            ->consumeDomainEvent(
                $output,
                $data
            );
    }
}
