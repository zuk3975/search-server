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

namespace Apisearch\Plugin\Callbacks\Tests\Unit\Adapter;

use Apisearch\Plugin\Callbacks\Domain\Adapter\HttpCommandAdapters;
use Apisearch\Plugin\Callbacks\Domain\Adapter\QueryHttpCommandAdapter;
use Apisearch\Server\Domain\Query\Query;
use PHPUnit\Framework\TestCase;

/**
 * Class HttpCommandAdaptersTest.
 */
class HttpCommandAdaptersTest extends TestCase
{
    /**
     * Test basic behavior.
     */
    public function testBasicBehavior()
    {
        $httpCommandAdapters = new HttpCommandAdapters();
        $httpCommandAdapters->addAdapter(new QueryHttpCommandAdapter());
        $this->assertInstanceOf(
            QueryHttpCommandAdapter::class,
            $httpCommandAdapters->getAdapter(Query::class)
        );
    }
}
