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
use Apisearch\Model\Changes;
use Apisearch\Query\Query as QueryModel;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\UpdateItems;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UpdateItemsController.
 */
class UpdateItemsController extends ControllerWithBusAndEventRepository
{
    /**
     * Update items.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws InvalidFormatException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $this->configureEventRepository($request);
        $query = $request->query;
        $requestBody = json_decode($request->getContent(), true);

        if (
            is_null($requestBody) ||
            !is_array($requestBody) ||
            !isset($requestBody[Http::QUERY_FIELD])
        ) {
            throw InvalidFormatException::queryFormatNotValid($request->getContent());
        }

        if (!isset($requestBody[Http::CHANGES_FIELD])) {
            throw InvalidFormatException::changesFormatNotValid($request->getContent());
        }

        $queryAsArray = $requestBody[Http::QUERY_FIELD];
        $changesAsArray = $requestBody[Http::CHANGES_FIELD];
        $this
            ->commandBus
            ->handle(new UpdateItems(
                RepositoryReference::create(
                    $query->get(Http::APP_ID_FIELD, ''),
                    $query->get(Http::INDEX_FIELD, '')
                ),
                $query->get(Http::TOKEN_FIELD, ''),
                QueryModel::createFromArray($queryAsArray),
                Changes::createFromArray($changesAsArray)
            ));

        return new JsonResponse('Items updated', 200);
    }
}
