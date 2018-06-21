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

namespace Apisearch\Plugin\Callbacks\DependencyInjection;

use Mmoreram\BaseBundle\DependencyInjection\BaseConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Class CallbacksPluginConfiguration.
 */
class CallbacksPluginConfiguration extends BaseConfiguration
{
    /**
     * Configure the root node.
     *
     * @param ArrayNodeDefinition $rootNode Root node
     */
    protected function setupTree(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->enumNode('http_client_adapter')
                    ->values(['http', 'http_test'])
                    ->defaultValue('http')
                ->end()
                ->arrayNode('callbacks')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('command')
                                ->isRequired()
                            ->end()
                            ->scalarNode('endpoint')
                                ->isRequired()
                            ->end()
                            ->scalarNode('method')
                                ->defaultValue('GET')
                            ->end()
                            ->enumNode('moment')
                                ->values(['before', 'after'])
                                ->isRequired()
                            ->end();
    }
}
