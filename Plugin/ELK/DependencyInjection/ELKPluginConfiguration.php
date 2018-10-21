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

namespace Apisearch\Plugin\ELK\DependencyInjection;

use Mmoreram\BaseBundle\DependencyInjection\BaseConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Class ELKPluginConfiguration.
 */
class ELKPluginConfiguration extends BaseConfiguration
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
                ->booleanNode('locator_enabled')
                    ->defaultTrue()
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
                ->end()
                ->scalarNode('key')
                    ->defaultValue('logstash:apisearch-domain-events')
                ->end();
    }
}
