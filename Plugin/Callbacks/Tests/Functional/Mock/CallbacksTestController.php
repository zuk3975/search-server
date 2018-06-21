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

namespace Apisearch\Plugin\Callbacks\Tests\Functional\Mock;

use Apisearch\Http\Http;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CallbacksTestController.
 */
class CallbacksTestController
{
    /**
     * @var Register
     *
     * Register
     */
    private $register;

    /**
     * CallbacksTestController constructor.
     *
     * @param Register $register
     */
    public function __construct(Register $register)
    {
        $this->register = $register;
    }

    public function emptyEndpoint(Request $request)
    {
        $this->addToRegister($request);

        return new JsonResponse();
    }

    /**
     * Manipulate query.
     */
    public function changeQuerySize(Request $request)
    {
        $this->addToRegister($request);

        $queryAsArray = json_decode($request->getContent(), true)[Http::QUERY_FIELD];
        $queryAsArray['size'] = 3;

        return new JsonResponse([
            Http::QUERY_FIELD => $queryAsArray,
        ]);
    }

    /**
     * Change query result with extra data.
     */
    public function changeQueryResult(Request $request)
    {
        $this->addToRegister($request);

        $resultAsArray = json_decode($request->getContent(), true);
        foreach ($resultAsArray['items'] as $itemId => $item) {
            $resultAsArray['items'][$itemId]['metadata']['modified'] = true;
        }

        return new JsonResponse($resultAsArray);
    }

    /**
     * Change token.
     */
    public function changeToken(Request $request)
    {
        $this->addToRegister($request);

        $newTokenAsArray = json_decode($request->getContent(), true)[Http::TOKEN_FIELD];
        $newTokenAsArray['uuid']['id'] .= '000';

        return new JsonResponse([
            Http::TOKEN_FIELD => $newTokenAsArray,
        ]);
    }

    /**
     * Change items.
     */
    public function changeItems(Request $request)
    {
        $this->addToRegister($request);

        $itemsAsArray = json_decode($request->getContent(), true)[Http::ITEMS_FIELD];
        foreach ($itemsAsArray as $index => $item) {
            $itemsAsArray[$index]['indexed_metadata']['flag'] = '1';
        }

        return new JsonResponse([
            Http::ITEMS_FIELD => $itemsAsArray,
        ]);
    }

    /**
     * Add to register.
     */
    public function addToRegister(Request $request)
    {
        $this
            ->register
            ->add([
                'method' => $request->getMethod(),
                'query' => $request->query,
                'request' => $request->request,
            ]);
    }
}
