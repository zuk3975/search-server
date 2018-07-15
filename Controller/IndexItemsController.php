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
use Apisearch\Model\Item;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\IndexItems;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class IndexItemsController.
 */
class IndexItemsController extends ControllerWithBus
{
    /**
     * Index items.
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
        $itemsAsArray = $this->getRequestContentObject(
            $request,
            Http::ITEMS_FIELD,
            InvalidFormatException::itemRepresentationNotValid($request->getContent()),
            []
        );

        $this
            ->commandBus
            ->handle(new IndexItems(
                RepositoryReference::create(
                    $query->get(Http::APP_ID_FIELD, ''),
                    $query->get(Http::INDEX_FIELD, '')
                ),
                $query->get(Http::TOKEN_FIELD, ''),
                array_map(function (array $object) {
                    return Item::createFromArray($object);
                }, $itemsAsArray)
            ));

        return new JsonResponse('Items indexed', 200);
    }
}
