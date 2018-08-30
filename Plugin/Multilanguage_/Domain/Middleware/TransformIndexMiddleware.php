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

namespace Apisearch\Plugin\Multilanguage\Domain\Middleware;

use Apisearch\Model\IndexUUID;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\DeleteIndex;
use Apisearch\Server\Domain\Command\DeleteItems;
use Apisearch\Server\Domain\Plugin\PluginMiddleware;

/**
 * Class TransformIndexMiddleware.
 */
class TransformIndexMiddleware implements PluginMiddleware
{
    /**
     * Execute middleware.
     *
     * @param mixed    $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute(
        $command,
        $next
    ) {
        $index = $command->getIndexUUID()->composeUUID();
        $indices = explode(',', $index);
        $indices = array_map(function (string $index) {
            return $index.'-plugin-language-*';
        }, $indices);

        $indices = implode(',', $indices);
        $command->setRepositoryReference(RepositoryReference::create(
            $command->getAppUUID(),
            IndexUUID::createById($indices)
        ));

        return $next($command);
    }

    /**
     * Events subscribed namespace. Can refer to specific class namespace, any
     * parent class or any interface.
     *
     * By returning an empty array, means coupled to all.
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [
            DeleteItems::class,
            DeleteIndex::class,
        ];
    }
}
