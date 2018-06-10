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
 * @author PuntMig Technologies
 */

declare(strict_types=1);

namespace Apisearch\Plugin\MetadataFields;

use Apisearch\Plugin\MetadataFields\DependencyInjection\CompilerPass\RepositoryCompilerPass;
use Apisearch\Plugin\MetadataFields\DependencyInjection\MetadataFieldsPluginExtension;
use Apisearch\Server\Domain\Plugin\Plugin;
use Mmoreram\BaseBundle\BaseBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Class MetadataFieldsPluginBundle.
 */
class MetadataFieldsPluginBundle extends BaseBundle implements Plugin
{
    /**
     * Returns the bundle's container extension.
     *
     * @return ExtensionInterface|null The container extension
     */
    public function getContainerExtension()
    {
        return new MetadataFieldsPluginExtension();
    }

    /**
     * Return a CompilerPass instance array.
     *
     * @return CompilerPassInterface[]
     */
    public function getCompilerPasses(): array
    {
        return [
            new RepositoryCompilerPass(),
        ];
    }

    /**
     * Get plugin name.
     *
     * @return string
     */
    public function getPluginName(): string
    {
        return 'metadata_fields';
    }
}
