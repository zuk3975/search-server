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

use Apisearch\Exception\TransportableException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Class PHPExceptionToJsonResponse.
 */
class PHPExceptionToJsonResponse
{
    /**
     * When controller gets exception.
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $exceptionErrorCode = $exception instanceof TransportableException
            ? $exception::getTransportableHTTPError()
            : 500;

        $event->setResponse(new JsonResponse([
            'message' => $exception->getMessage(),
            'code' => $exceptionErrorCode,
        ], $exceptionErrorCode));

        $event->stopPropagation();
    }
}
