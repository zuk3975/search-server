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

namespace Apisearch\Plugin\Callbacks;

use Apisearch\Plugin\Callbacks\DependencyInjection\CallbacksPluginExtension;
use Apisearch\Plugin\Callbacks\DependencyInjection\CompilerPass\HttpClientAdapterCompilerPass;
use Apisearch\Plugin\Callbacks\DependencyInjection\CompilerPass\HttpCommandAdapterCompilerPass;
use Apisearch\Server\Domain\Plugin\Plugin;
use Mmoreram\BaseBundle\BaseBundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Class CallbacksPluginBundle.
 */
class CallbacksPluginBundle extends BaseBundle implements Plugin
{
    /**
     * Returns the bundle's container extension.
     *
     * @return ExtensionInterface|null The container extension
     */
    public function getContainerExtension()
    {
        return new CallbacksPluginExtension();
    }

    public function getCompilerPasses(): array
    {
        return [
            new HttpClientAdapterCompilerPass(),
            new HttpCommandAdapterCompilerPass(),
        ];
    }

    /**
     * Get plugin name.
     *
     * @return string
     */
    public function getPluginName(): string
    {
        return 'callbacks';
    }
}
