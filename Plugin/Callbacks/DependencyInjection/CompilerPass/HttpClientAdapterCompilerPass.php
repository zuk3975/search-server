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

namespace Apisearch\Plugin\Callbacks\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class HttpClientAdapterCompilerPass.
 */
class HttpClientAdapterCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        $container
            ->addAliases([
                'apisearch_plugin.callbacks.http_client_adapter' => 'http' === $container->getParameter('apisearch_plugin_callbacks.http_client_adapter')
                    ? 'apisearch_plugin.callbacks.guzzle_http_adapter'
                    : 'apisearch_plugin.callbacks.test_http_adapter',
            ]);
    }
}
