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

namespace Apisearch\Plugin\Callbacks\Domain\Adapter;

/**
 * Class HttpCommandAdapters.
 */
class HttpCommandAdapters
{
    /**
     * @var HttpCommandAdapter[]
     *
     * Adapters
     */
    private $adapters;

    /**
     * Add adapter.
     *
     * @param HttpCommandAdapter $adapter
     */
    public function addAdapter(HttpCommandAdapter $adapter)
    {
        $this->adapters[$adapter->getCommandNamespace()] = $adapter;
    }

    /**
     * Get adapter by command name.
     *
     * @param string $commandName
     *
     * @return HttpCommandAdapter|null
     */
    public function getAdapter(string $commandName): ? HttpCommandAdapter
    {
        return $this->adapters[$commandName] ?? null;
    }
}
