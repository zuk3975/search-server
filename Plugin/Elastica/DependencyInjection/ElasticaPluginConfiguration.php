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

namespace Apisearch\Plugin\Elastica\DependencyInjection;

use Mmoreram\BaseBundle\DependencyInjection\BaseConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Class ElasticaPluginConfiguration.
 */
class ElasticaPluginConfiguration extends BaseConfiguration
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
                ->scalarNode('repository_config_path')
                    ->defaultValue('{root}/elastic/{app_id}/{index_id}/')
                ->end()
                ->booleanNode('save_events')
                    ->defaultFalse()
                ->end()
                ->arrayNode('cluster')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('host')
                                ->defaultTrue()
                            ->end()
                            ->scalarNode('port')
                                ->defaultNull()
                            ->end()
                        ->end()
                    ->end()
                ->end();
    }
}
