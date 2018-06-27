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

use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\DeleteItems;
use Apisearch\Server\Domain\Plugin\PluginMiddleware;

/**
 * Class DeleteItemsMiddleware.
 */
class DeleteItemsMiddleware implements PluginMiddleware
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
        $index = $command->getIndex();
        $indices = explode(',', $index);
        $indices = array_map(function (string $index) {
            return $index.'_language_*';
        }, $indices);

        $indices = implode(',', $indices);
        $command->setRepositoryReference(RepositoryReference::create(
            $command->getAppId(),
            $indices
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
        return [DeleteItems::class];
    }
}
