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

use Apisearch\Http\Http;
use Apisearch\Model\AppUUID;
use Apisearch\Model\IndexUUID;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\ResetIndex;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ResetIndexController.
 */
class ResetIndexController extends ControllerWithBus
{
    /**
     * Reset the index.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $query = $request->query;

        $this
            ->commandBus
            ->handle(new ResetIndex(
                RepositoryReference::create(
                    AppUUID::createById($query->get(Http::APP_ID_FIELD, '')),
                    IndexUUID::createById($query->get(Http::INDEX_FIELD, ''))
                ),
                $query->get(Http::TOKEN_FIELD, ''),
                IndexUUID::createById($query->get(Http::INDEX_FIELD, ''))
            ));

        return new JsonResponse('Index reset', 200);
    }
}
