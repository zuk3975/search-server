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

namespace Apisearch\Server\Tests\Functional\Http;

use Apisearch\Query\Query;
use Apisearch\Server\Tests\Functional\InaccessibleHttpFunctionalTest;

/**
 * Class InaccessibleServerTest.
 */
class InaccessibleServerTest extends InaccessibleHttpFunctionalTest
{
    /**
     * Test check health with different tokens.
     *
     * @expectedException \Apisearch\Exception\ConnectionException
     */
    public function testSimpleQuery(): void
    {
        $this->query(Query::createMatchAll());
    }
}
