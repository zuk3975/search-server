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

namespace Apisearch\Plugin\NewRelic;

use Apisearch\Plugin\NewRelic\DependencyInjection\CompilerPass\NewRelicParamsCompilerPass;
use Apisearch\Server\ApisearchServerBundle;
use Apisearch\Server\Domain\Plugin\Plugin;
use Ekino\NewRelicBundle\EkinoNewRelicBundle;
use Mmoreram\BaseBundle\SimpleBaseBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class NewRelicPluginBundle.
 */
class NewRelicPluginBundle extends SimpleBaseBundle implements Plugin
{
    /**
     * Return all bundle dependencies.
     *
     * Values can be a simple bundle namespace or its instance
     *
     * @param KernelInterface $kernel
     *
     * @return array
     */
    public static function getBundleDependencies(KernelInterface $kernel): array
    {
        return [
            ApisearchServerBundle::class,
            EkinoNewRelicBundle::class,
        ];
    }

    /**
     * get config files.
     *
     * @return array
     */
    public function getConfigFiles(): array
    {
        return [
            'listeners',
        ];
    }

    /**
     * Get plugin name.
     *
     * @return string
     */
    public function getPluginName(): string
    {
        return 'new_relic';
    }

    /**
     * Return a CompilerPass instance array.
     *
     * @return CompilerPassInterface[]
     */
    public function getCompilerPasses(): array
    {
        return [
            new NewRelicParamsCompilerPass(),
        ];
    }
}
