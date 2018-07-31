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

namespace Apisearch\Plugin\NewRelic\DependencyInjection;

use Mmoreram\BaseBundle\DependencyInjection\BaseExtension;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class NewRelicPluginExtension.
 */
class NewRelicPluginExtension extends BaseExtension
{
    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     */
    public function getAlias()
    {
        return 'apisearch_plugin_newrelic';
    }

    /**
     * Return a new Configuration instance.
     *
     * If object returned by this method is an instance of
     * ConfigurationInterface, extension will use the Configuration to read all
     * bundle config definitions.
     *
     * Also will call getParametrizationValues method to load some config values
     * to internal parameters.
     *
     * @return ConfigurationInterface|null
     */
    protected function getConfigurationInstance(): ? ConfigurationInterface
    {
        return new NewRelicPluginConfiguration($this->getAlias());
    }

    /**
     * Load Parametrization definition.
     *
     * return array(
     *      'parameter1' => $config['parameter1'],
     *      'parameter2' => $config['parameter2'],
     *      ...
     * );
     *
     * @param array $config Bundles config values
     *
     * @return array
     */
    protected function getParametrizationValues(array $config): array
    {
        return [
            'apisearch_plugin.newrelic.application_name' => $_ENV['NEWRELIC_APP_NAME'] ?? $config['application_name'],
            'apisearch_plugin.newrelic.api_key' => $_ENV['NEWRELIC_API_KEY'] ?? $config['api_key'],
            'apisearch_plugin.newrelic.license_key' => $_ENV['NEWRELIC_LICENSE_KEY'] ?? $config['license_key'],
        ];
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        parent::prepend($container);

        $container->prependExtensionConfig('ekino_new_relic', [
            'enabled' => true,
            'application_name' => $container->getParameter('apisearch_plugin.newrelic.application_name'),
            'api_key' => $container->getParameter('apisearch_plugin.newrelic.api_key'),
            'license_key' => $container->getParameter('apisearch_plugin.newrelic.license_key'),
            'http' => [
                'using_symfony_cache' => true,
            ],
        ]);
    }
}
