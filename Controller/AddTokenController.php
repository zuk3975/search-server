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
 * @author PuntMig Technologies
 */

declare(strict_types=1);

namespace Apisearch\Server\Controller;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Http\Http;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\AddToken;
use Apisearch\Token\Token;
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
        $requestBody = json_decode($request->getContent(), true);

        if (
            null === $requestBody ||
            !isset($requestBody[Http::TOKEN_FIELD])
        ) {
            throw InvalidFormatException::tokenFormatNotValid($request->getContent());
        }

        $newTokenAsArray = $requestBody[Http::TOKEN_FIELD];
        $this
            ->commandBus
            ->handle(new AddToken(
                RepositoryReference::create(
                    $query->get(Http::APP_ID_FIELD, ''),
                    ''
                ),
                $query->get(Http::TOKEN_FIELD, ''),
                Token::createFromArray($newTokenAsArray)
            ));

        return new JsonResponse('Token added', 200);
    }
}
