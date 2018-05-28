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
    public function updateItems(Request $request): JsonResponse
    {
        $this->configureEventRepository($request);
        $query = $request->query;
        $requestBody = $request->request;
        $plainQuery = $requestBody->get(Http::QUERY_FIELD, null);
        if (!is_string($plainQuery)) {
            throw InvalidFormatException::queryFormatNotValid(json_encode($plainQuery));
        }

        $changes = $requestBody->get(Http::CHANGES_FIELD, null);
        if (!is_string($changes)) {
            throw InvalidFormatException::changesFormatNotValid(json_encode($changes));
        }

        $this
            ->commandBus
            ->handle(new UpdateItems(
                RepositoryReference::create(
                    $query->get(Http::APP_ID_FIELD),
                    $query->get(Http::INDEX_FIELD)
                ),
                $query->get('token'),
                QueryModel::createFromArray(json_decode($plainQuery, true)),
                Changes::createFromArray(json_decode($plainQuery, true))
            ));

        return new JsonResponse('Items updated', 200);
    }
}
