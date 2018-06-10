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
 * @author PuntMig Technologies
 */

declare(strict_types=1);

namespace Apisearch\Server\Domain\Plugin;

use Apisearch\Server\Domain\CommandWithRepositoryReferenceAndToken;

/**
 * Interface PluginMiddleware.
 */
interface PluginMiddleware
{
    /**
     * Execute middleware.
     *
     * @param CommandWithRepositoryReferenceAndToken $command
     * @param callable                               $next
     *
     * @return mixed
     */
    public function execute(
        CommandWithRepositoryReferenceAndToken $command,
        $next
    );

    /**
     * Event subscribed namespace.
     *
     * @return string
     */
    public function getSubscribedEvent(): string;
}
