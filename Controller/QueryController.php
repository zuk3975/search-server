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
use Apisearch\Query\Query as QueryModel;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Query\Query;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class QueryController.
 */
class QueryController extends ControllerWithBus
{
    /**
     * Make a query.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws InvalidFormatException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $query = $request->query;

        $queryAsArray = $this->getRequestContentObject(
            $request,
            Http::QUERY_FIELD,
            InvalidFormatException::queryFormatNotValid($request->getContent()),
            []
        );

        $responseAsArray = $this
            ->commandBus
            ->handle(new Query(
                RepositoryReference::create(
                    $query->get(Http::APP_ID_FIELD, ''),
                    $query->get(Http::INDEX_FIELD, '*')
                ),
                $query->get(Http::TOKEN_FIELD, ''),
                QueryModel::createFromArray($queryAsArray)
            ))
            ->toArray();

        if ($query->has(Http::PURGE_QUERY_FROM_RESPONSE_FIELD)) {
            unset($responseAsArray[Http::QUERY_FIELD]);
        }

        return new JsonResponse(
            $responseAsArray,
            200,
            [
                'Access-Control-Allow-Origin' => '*',
            ]
        );
    }
}
