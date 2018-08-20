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

use Apisearch\Config\ImmutableConfig;
use Apisearch\Exception\InvalidFormatException;
use Apisearch\Http\Http;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\CreateIndex;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CreateIndexController.
 */
class CreateIndexController extends ControllerWithBus
{
    /**
     * Create an index.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $query = $request->query;

        $configAsArray = $this->getRequestContentObject(
            $request,
            Http::CONFIG_FIELD,
            InvalidFormatException::configFormatNotValid($request->getContent()),
            []
        );

        $this
            ->commandBus
            ->handle(new CreateIndex(
                RepositoryReference::create(
                    $query->get(Http::APP_ID_FIELD, ''),
                    $query->get(Http::INDEX_FIELD, '')
                ),
                $query->get(Http::TOKEN_FIELD, ''),
                ImmutableConfig::createFromArray($configAsArray)
            ));

        return new JsonResponse('Index created', JsonResponse::HTTP_CREATED);
    }
}
