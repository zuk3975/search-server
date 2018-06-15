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

namespace Apisearch\Server\Controller;

use Apisearch\Server\Domain\Query\Ping;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PingController.
 */
class PingController extends ControllerWithBus
{
    /**
     * Ping.
     *
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $alive = $this
            ->commandBus
            ->handle(new Ping());

        return true === $alive
            ? new Response('', Response::HTTP_OK)
            : new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
