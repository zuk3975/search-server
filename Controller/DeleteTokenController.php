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

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Http\Http;
use Apisearch\Model\AppUUID;
use Apisearch\Model\TokenUUID;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\DeleteToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DeleteTokenController.
 */
class DeleteTokenController extends ControllerWithBus
{
    /**
     * Delete a token.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $query = $request->query;
        $tokenUUIDAsArray = $this->getRequestContentObject(
            $request,
            Http::TOKEN_FIELD,
            InvalidFormatException::tokenUUIDFormatNotValid($request->getContent())
        );

        $this
            ->commandBus
            ->handle(new DeleteToken(
                RepositoryReference::create(
                    AppUUID::createById($query->get(Http::APP_ID_FIELD, ''))
                ),
                $query->get(Http::TOKEN_FIELD, ''),
                TokenUUID::createFromArray($tokenUUIDAsArray)
            ));

        return new JsonResponse('Token deleted', 200);
    }
}
