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
use Apisearch\Model\Token;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\AddToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AddTokenController.
 */
class AddTokenController extends ControllerWithBus
{
    /**
     * Add a token.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $query = $request->query;
        $newTokenAsArray = $this->getRequestContentObject(
            $request,
            Http::TOKEN_FIELD,
            InvalidFormatException::tokenFormatNotValid($request->getContent())
        );

        $this
            ->commandBus
            ->handle(new AddToken(
                RepositoryReference::create(
                    AppUUID::createById($query->get(Http::APP_ID_FIELD, ''))
                ),
                $query->get(Http::TOKEN_FIELD, ''),
                Token::createFromArray($newTokenAsArray)
            ));

        return new JsonResponse('Token added', 200);
    }
}
