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

namespace Apisearch\Server\Tests\Functional\Domain\Repository;

use Apisearch\Model\Index;

/**
 * Class IndexStatusTest.
 */
trait GetIndicesTest
{
    /**
     * Test index check.
     *
     * @group hola
     */
    public function testGetIndicesWithAppid(): void
    {
        $indices = $this->getIndices(self::$appId);
        $this->assertTrue(count($indices) >= 2);
        $index = array_shift($indices);
        $this->assertInstanceOf(Index::class, $index);
    }

    /**
     * Test index check.
     */
    public function testGetIndices(): void
    {
        $indices = array_values($this->getIndices(''));
        $this->assertTrue(count($indices) >= 2);
    }
}
