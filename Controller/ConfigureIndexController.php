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

use Apisearch\Config\Config;
use Apisearch\Exception\InvalidFormatException;
use Apisearch\Http\Http;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\ConfigureIndex;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ConfigureIndexController.
 */
class ConfigureIndexController extends ControllerWithBus
{
    /**
     * Config the index.
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
            InvalidFormatException::configFormatNotValid($request->getContent())
        );

        $this
            ->commandBus
            ->handle(new ConfigureIndex(
                RepositoryReference::create(
                    $query->get(Http::APP_ID_FIELD, ''),
                    $query->get(Http::INDEX_FIELD, '')
                ),
                $query->get(Http::TOKEN_FIELD, ''),
                Config::createFromArray($configAsArray)
            ));

        return new JsonResponse('Config applied', 200);
    }
}
