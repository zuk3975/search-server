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

namespace Apisearch\Plugin\NewRelic\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class NewRelicParamsCompilerPass.
 */
class NewRelicParamsCompilerPass implements CompilerPassInterface
{
    /**
     * Process.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('ekino_new_relic', [
            'enabled' => true,
            'application_name' => $_ENV['NEWRELIC_APP_NAME'],
            'api_key' => $_ENV['NEWRELIC_API_KEY'],
            'license_key' => $_ENV['NEWRELIC_LICENSE_KEY'],
            'http' => [
                'using_symfony_cache' => true,
            ],
        ]);
    }
}
