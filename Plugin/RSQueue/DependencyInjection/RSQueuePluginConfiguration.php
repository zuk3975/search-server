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

namespace Apisearch\Plugin\RSQueue\DependencyInjection;

use Mmoreram\BaseBundle\DependencyInjection\BaseConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Class RSQueuePluginConfiguration.
 */
class RSQueuePluginConfiguration extends BaseConfiguration
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
                ->booleanNode('commands_queue_name')
                    ->defaultValue('apisearch:server:commands')
                ->end()
                ->booleanNode('events_queue_name')
                    ->defaultValue('apisearch:server:domain-events')
                ->end()
                ->scalarNode('host')
                    ->defaultNull()
                ->end()
                ->integerNode('port')
                    ->defaultNull()
                ->end()
                ->booleanNode('is_cluster')
                    ->defaultFalse()
                ->end()
                ->scalarNode('database')
                    ->defaultNull()
                ->end();
    }
}
