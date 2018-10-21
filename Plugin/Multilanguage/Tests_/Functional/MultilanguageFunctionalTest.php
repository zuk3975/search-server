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

namespace Apisearch\Plugin\Multilanguage\Tests\Functional;

use Apisearch\Plugin\Multilanguage\MultilanguagePluginBundle;
use Apisearch\Server\Tests\Functional\AsynchronousFunctionalTest;

/**
 * Class MultilanguageFunctionalTest.
 */
abstract class MultilanguageFunctionalTest extends AsynchronousFunctionalTest
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
        $bundles[] = MultilanguagePluginBundle::class;

        return $bundles;
    }

    /**
     * Save events.
     *
     * @return bool
     */
    protected static function saveEvents(): bool
    {
        return false;
    }

    /**
     * Get items file path.
     *
     * @return string
     */
    public static function getItemsFilePath(): string
    {
        return __DIR__.'/items.yml';
    }
}
