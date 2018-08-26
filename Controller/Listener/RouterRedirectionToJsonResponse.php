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

namespace Apisearch\Server\Controller\Listener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class RouterRedirectionToJsonResponse.
 */
class RouterRedirectionToJsonResponse
{
    /**
     * Intercepting redirects.
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        if ($response instanceof RedirectResponse) {
            if (Response::HTTP_MOVED_PERMANENTLY === $response->getStatusCode()) {
                $location =
                    explode('?', $response->getTargetUrl())[0].'?'.
                    explode('?', $event->getRequest()->getRequestUri(), 2)[1];

                $response->headers->set('location', $location);
                $event->setResponse(new JsonResponse(
                    [
                        'code' => Response::HTTP_MOVED_PERMANENTLY,
                        'message' => 'Moved Permanently',
                    ],
                    Response::HTTP_MOVED_PERMANENTLY,
                    $response->headers->all()
                ));
            }
        }
    }
}
