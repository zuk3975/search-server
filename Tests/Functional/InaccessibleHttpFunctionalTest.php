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

namespace Apisearch\Server\Tests\Functional;

/**
 * Class InaccessibleHttpFunctionalTest.
 */
abstract class InaccessibleHttpFunctionalTest extends HttpFunctionalTest
{
    /**
     * Get repository name.
     *
     * @return string
     */
    protected static function getRepositoryName(): string
    {
        return 'search_inaccessible';
    }

    /**
     * Reset scenario.
     */
    public static function resetScenario()
    {
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public static function tearDownAfterClass()
    {
    }
}
