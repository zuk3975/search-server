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
     */
    public function testGetIndices(): void
    {
        $indexCreated = false;
        if (!$this->checkIndex(self::$appId, self::$index)) {
            $this->createIndex(self::$appId, self::$index);
            $indexCreated = true;
        }
        $indices = array_values($this->getIndices(self::$appId));
        /**
         * @var Index|null $index
         */
        $index = array_shift($indices);

        $this->assertNotNull($index);
        $this->assertInstanceOf(Index::class, $index);
        $this->assertNotNull($index->getAppId());
        $this->assertNotNull($index->getName());
        if ($indexCreated) {
            $this->deleteIndex(self::$appId, self::$index);
        }
    }
}
