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

use Apisearch\Server\Exception\StorableException;

/**
 * Class ExceptionWasCached.
 */
class ExceptionWasCached extends DomainEvent
{
    /**
     * @var StorableException
     *
     * Exception
     */
    private $exception;

    /**
     * ExceptionWasCached constructor.
     *
     * @param StorableException $exception
     */
    public function __construct(StorableException $exception)
    {
        $this->setNow();
        $this->exception = $exception;
    }

    /**
     * Indexable to array.
     *
     * @return array
     */
    public function readableOnlyToArray(): array
    {
        return [
            'exception' => [
                'message' => $this->exception->getMessage(),
                'code' => $this->exception->getCode(),
                'trace_as_string' => $this->exception->getTraceAsString(),
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
            ],
        ];
    }

    /**
     * Indexable to array.
     *
     * @return array
     */
    public function indexableToArray(): array
    {
        return [
            'message' => $this->exception->getMessage(),
            'code' => $this->exception->getCode(),
        ];
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
        $payload = json_decode($data, true);

        return [
            new StorableException(
                $payload['exception']['message'],
                $payload['exception']['code'],
                $payload['exception']['trace_as_string'],
                $payload['exception']['file'],
                $payload['exception']['line']
            ),
        ];
    }
}
