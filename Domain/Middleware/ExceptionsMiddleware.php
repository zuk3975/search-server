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

namespace Apisearch\Server\Domain\Middleware;

use Apisearch\Server\Domain\Event\DomainEventWithRepositoryReference;
use Apisearch\Server\Domain\Event\EventPublisher;
use Apisearch\Server\Domain\Event\ExceptionWasCached;
use Apisearch\Server\Exception\StorableException;
use Exception;
use League\Tactician\Middleware;

/**
 * Class ExceptionsMiddleware.
 */
class ExceptionsMiddleware implements Middleware
{
    /**
     * @var EventPublisher
     *
     * Event publisher
     */
    protected $eventPublisher;

    /**
     * QueryHandler constructor.
     *
     * @param EventPublisher $eventPublisher
     */
    public function __construct(EventPublisher $eventPublisher)
    {
        $this->eventPublisher = $eventPublisher;
    }

    /**
     * @param object   $command
     * @param callable $next
     *
     * @return mixed
     *
     * @throws Exception Any exception
     */
    public function execute($command, callable $next)
    {
        try {
            $result = $next($command);
        } catch (Exception $exception) {
            $this
                ->eventPublisher
                ->publish(new DomainEventWithRepositoryReference(
                    $command->getRepositoryReference(),
                    new ExceptionWasCached(new StorableException(
                        $exception->getMessage(),
                        (int) $exception->getCode(),
                        $exception->getTraceAsString(),
                        $exception->getFile(),
                        (int) $exception->getLine()
                    ))
                ));

            throw $exception;
        }

        return $result;
    }
}
