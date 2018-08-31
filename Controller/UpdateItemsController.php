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
use Apisearch\Model\Changes;
use Apisearch\Model\IndexUUID;
use Apisearch\Query\Query as QueryModel;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\UpdateItems;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UpdateItemsController.
 */
class UpdateItemsController extends ControllerWithBus
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
        $query = $request->query;
        $queryAsArray = $this->getRequestContentObject(
            $request,
            Http::QUERY_FIELD,
            InvalidFormatException::queryFormatNotValid($request->getContent())
        );

        $changesAsArray = $this->getRequestContentObject(
            $request,
            Http::CHANGES_FIELD,
            InvalidFormatException::queryFormatNotValid($request->getContent())
        );

        $this
            ->commandBus
            ->handle(new UpdateItems(
                RepositoryReference::create(
                    AppUUID::createById($query->get(Http::APP_ID_FIELD, '')),
                    IndexUUID::createById($query->get(Http::INDEX_FIELD, '*'))
                ),
                $query->get(Http::TOKEN_FIELD, ''),
                QueryModel::createFromArray($queryAsArray),
                Changes::createFromArray($changesAsArray)
            ));

        return new JsonResponse('Items updated', 200);
    }
}
