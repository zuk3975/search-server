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

namespace Apisearch\Plugin\MostRelevantWords\DependencyInjection;

use Mmoreram\BaseBundle\DependencyInjection\BaseConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Class MostRelevantWordsPluginConfiguration.
 */
class MostRelevantWordsPluginConfiguration extends BaseConfiguration
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
                ->arrayNode('fields')
                    ->arrayPrototype()
                        ->children()
                            ->integerNode('maximum_words')
                                ->isRequired()
                            ->end()
                            ->integerNode('minimum_frequency')
                                ->isRequired()
                            ->end()
                            ->integerNode('minimum_length')
                                ->isRequired()
                            ->end()
                            ->booleanNode('keep_relative_positions')
                                ->defaultTrue()
                            ->end();
    }
}
