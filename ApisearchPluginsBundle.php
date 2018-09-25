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

namespace Apisearch\Server;

use Apisearch\Plugin\Callbacks\CallbacksPluginBundle;
use Apisearch\Plugin\Elastica\ElasticaPluginBundle;
use Apisearch\Plugin\MetadataFields\MetadataFieldsPluginBundle;
use Apisearch\Plugin\MostRelevantWords\MostRelevantWordsPluginBundle;
use Apisearch\Plugin\Multilanguage\MultilanguagePluginBundle;
use Apisearch\Plugin\Neo4j\Neo4jPluginBundle;
use Apisearch\Plugin\NewRelic\NewRelicPluginBundle;
use Apisearch\Plugin\Redis\RedisPluginBundle;
use Apisearch\Server\Domain\Plugin\Plugin;
use Mmoreram\BaseBundle\BaseBundle;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ApisearchPluginsBundle.
 */
class ApisearchPluginsBundle extends BaseBundle
{
    /**
     * Return all bundle dependencies.
     *
     * Values can be a simple bundle namespace or its instance
     *
     * @return array
     */
    public static function getBundleDependencies(KernelInterface $kernel): array
    {
        $plugins = [
            CallbacksPluginBundle::class,
            ElasticaPluginBundle::class,
            RedisPluginBundle::class,
        ];

        $pluginsAsString = $_ENV['APISEARCH_ENABLED_PLUGINS'] ?? '';
        $pluginsAsArray = explode(',', $pluginsAsString);
        $pluginsAsArray = array_map('trim', $pluginsAsArray);
        $pluginsAsArray = self::resolveAliases($pluginsAsArray);

        $pluginsAsArray = array_filter($pluginsAsArray, function (string $pluginNamespace) {
            if (
                empty($pluginNamespace) ||
                !class_exists($pluginNamespace)
            ) {
                return false;
            }

            $reflectionClass = new \ReflectionClass($pluginNamespace);

            return $reflectionClass->implementsInterface(Plugin::class);
        });

        return array_merge($plugins, $pluginsAsArray);
    }

    /**
     * Resolve aliases.
     *
     * @param array $bundles
     *
     * @return array
     */
    private static function resolveAliases(array $bundles): array
    {
        $aliases = [
            'metadata_fields' => MetadataFieldsPluginBundle::class,
            'multilanguage' => MultilanguagePluginBundle::class,
            'most_relevant_words' => MostRelevantWordsPluginBundle::class,
            'newrelic' => NewRelicPluginBundle::class,
            'neo4j' => Neo4jPluginBundle::class,
        ];

        $combined = array_combine(
            array_values($bundles),
            array_values($bundles)
        );

        return array_values(
            array_replace(
                $combined,
                array_intersect_key(
                    $aliases,
                    $combined
                )
            )
        );
    }
}
