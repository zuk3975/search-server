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

namespace Apisearch\Plugin\Callbacks\Tests\Functional;

use Apisearch\Plugin\Callbacks\CallbacksPluginBundle;
use Apisearch\Plugin\Callbacks\Tests\Functional\Mock\CallbacksTestController;
use Apisearch\Plugin\Callbacks\Tests\Functional\Mock\Register;
use Apisearch\Server\Tests\Functional\ServiceFunctionalTest;

/**
 * Class EndpointsFunctionalTest.
 */
abstract class EndpointsFunctionalTest extends ServiceFunctionalTest
{
    /**
     * Decorate bundles.
     *
     * @param array $bundles
     *
     * @return array
     */
    protected static function decorateBundles(array $bundles): array
    {
        $bundles[] = CallbacksPluginBundle::class;

        return $bundles;
    }

    /**
     * Decorate configuration.
     *
     * @param array $configuration
     *
     * @return array
     */
    protected static function decorateConfiguration(array $configuration): array
    {
        $configuration['services']['apisearch_plugin.callbacks_test_register'] = [
            'class' => Register::class,
        ];

        $configuration['services']['apisearch_plugin.callbacks_test_controller'] = [
            'class' => CallbacksTestController::class,
            'arguments' => [
                '@apisearch_plugin.callbacks_test_register',
            ],
        ];

        $configuration['apisearch_plugin_callbacks'] = static::getCallbacksConfiguration();

        return $configuration;
    }

    /**
     * Decorate routes.
     *
     * @param array $routes
     *
     * @return array
     */
    protected static function decorateRoutes(array $routes): array
    {
        $routes[] = '@CallbacksPluginBundle/Resources/test/routing.yml';

        return $routes;
    }

    /**
     * Get controller register.
     *
     * @return array
     */
    protected function getRegister()
    {
        return $this
            ->get('apisearch_plugin.callbacks_test_register')
            ->get();
    }

    /**
     * Flush controller register.
     *
     * @return array
     */
    protected function flushRegister()
    {
        $this
            ->get('apisearch_plugin.callbacks_test_register')
            ->flush();
    }

    /**
     * Return query.
     */
    protected static function getUrlQuery(): string
    {
        return 'app_id='.self::$appId.'&index='.self::$index.'&token='.self::$godToken;
    }

    /**
     * Get callbacks configuration.
     *
     * @return array
     */
    abstract protected static function getCallbacksConfiguration(): array;
}
