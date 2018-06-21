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

namespace Apisearch\Plugin\Callbacks\Domain;

/**
 * Class Callbacks.
 */
class Callbacks
{
    /**
     * @var array
     *
     * Callbacks
     */
    private $callbacks;

    /**
     * Callbacks constructor.
     *
     * @param array $callbacks
     */
    public function __construct(array $callbacks)
    {
        $this->callbacks = $callbacks;
    }

    /**
     * Get callbacks given a command and a moment.
     *
     * @param string $command
     * @param string $moment
     *
     * @return array
     */
    public function getCommandCallbacksForMoment(
        string $command,
        string $moment
    ) {
        return array_filter($this->callbacks, function (array $callback) use ($command, $moment) {
            return
                $callback['command'] === $command &&
                $callback['moment'] === $moment;
        });
    }
}
