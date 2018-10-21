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

namespace Apisearch\Plugin\RSQueue;

use Apisearch\Plugin\Redis\RedisBundle;
use Apisearch\Plugin\RSQueue\DependencyInjection\RSQueuePluginExtension;
use Apisearch\Server\ApisearchServerBundle;
use Apisearch\Server\Domain\Plugin\Plugin;
use Apisearch\Server\Domain\Plugin\QueuePlugin;
use Mmoreram\BaseBundle\BaseBundle;
use RSQueueBundle\RSQueueBundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class RSQueuePluginBundle.
 */
class RSQueuePluginBundle extends BaseBundle implements Plugin, QueuePlugin
{
    /**
     * Return all bundle dependencies.
     *
     * Values can be a simple bundle namespace or its instance
     *
     * @param KernelInterface $kernel
     *
     * @return array
     */
    public static function getBundleDependencies(KernelInterface $kernel): array
    {
        return [
            ApisearchServerBundle::class,
            RedisBundle::class,
            RSQueueBundle::class,
        ];
    }

    /**
     * Returns the bundle's container extension.
     *
     * @return ExtensionInterface|null The container extension
     *
     * @throws \LogicException
     */
    public function getContainerExtension()
    {
        return new RSQueuePluginExtension();
    }

    /**
     * Get plugin name.
     *
     * @return string
     */
    public function getPluginName(): string
    {
        return 'rsqueue';
    }
}
