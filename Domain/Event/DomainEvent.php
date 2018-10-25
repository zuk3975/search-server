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

use Apisearch\Server\Domain\Now;
use Carbon\Carbon;
use Exception;
use ReflectionClass;

/**
 * Abstract class DomainEvent.
 */
abstract class DomainEvent
{
    /**
     * @var int
     *
     * Occurred on
     */
    private $occurredOn;

    /**
     * @var Carbon;
     *
     * Now
     */
    private $now;

    /**
     * Mark occurred on as now.
     */
    protected function setNow()
    {
        $this->now = Carbon::now('UTC');
        $this->occurredOn = Now::epochTimeWithMicroseconds($this->now);
    }

    /**
     * Return when event occurred.
     *
     * @return int
     */
    public function occurredOn(): int
    {
        return $this->occurredOn;
    }

    /**
     * Return specific occurred_on ranges.
     *
     * @return int[]
     */
    public function occurredOnRanges(): array
    {
        return [
            'occurred_on_day' => $this->now->startOfDay()->timestamp,
            'occurred_on_week' => $this->now->startOfWeek()->timestamp,
            'occurred_on_month' => $this->now->startOfMonth()->timestamp,
            'occurred_on_year' => $this->now->startOfYear()->timestamp,
        ];
    }

    /**
     * From array.
     *
     * @param array $data
     *
     * @return mixed
     */
    public static function fromArray(array $data)
    {
        $namespace = 'Apisearch\Server\Domain\Event\\'.$data['type'];

        return $namespace::createByPlainValues(
            $data['occurred_on'],
            $data['payload']
        );
    }

    /**
     * Create by plain values.
     *
     * @param int   $occurredOn
     * @param array $payload
     *
     * @return static
     */
    public static function createByPlainValues(
        int $occurredOn,
        array $payload
    ) {
        $reflector = new ReflectionClass(static::class);
        $instance = $reflector->newInstanceArgs(static::fromArrayPayload($payload));
        $instance->occurredOn = $occurredOn;

        return $instance;
    }

    /**
     * to array payload.
     *
     * @return array
     */
    abstract public function toArrayPayload(): array;

    /**
     * To payload.
     *
     * @param array $arrayPayload
     *
     * @return array
     *
     * @throws Exception
     */
    public static function fromArrayPayload(array $arrayPayload): array
    {
        throw new Exception('Your domain event MUST implement the method fromArrayPayload');
    }

    /**
     * To plan values.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => str_replace('Apisearch\Server\Domain\Event\\', '', get_class($this)),
            'occurred_on' => $this->occurredOn(),
            'payload' => $this->toArrayPayload(),
        ];
    }

    /**
     * To logger.
     *
     * @return array
     */
    public function toLogger(): array
    {
        return [
            'type' => str_replace('Apisearch\Server\Domain\Event\\', '', get_class($this)),
            'occurred_on' => $this->occurredOn(),
        ] + $this->toArrayPayload();
    }
}
