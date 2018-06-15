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

namespace Apisearch\Plugin\MetadataFields\Tests\Functional;

use Apisearch\Plugin\MetadataFields\MetadataFieldsPluginBundle;
use Apisearch\Server\Tests\Functional\ServiceFunctionalTest;

/**
 * Class MetadataFieldsFunctionalTest.
 */
abstract class MetadataFieldsFunctionalTest extends ServiceFunctionalTest
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
        $bundles[] = MetadataFieldsPluginBundle::class;

        return $bundles;
    }
}
