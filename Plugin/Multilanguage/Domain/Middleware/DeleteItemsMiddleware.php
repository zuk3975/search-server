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
use Apisearch\Server\Domain\Command\DeleteItems;
use Apisearch\Server\Domain\CommandEnqueuer\CommandEnqueuer;
use Apisearch\Server\Domain\Plugin\PluginMiddleware;

/**
 * Class DeleteItemsMiddleware.
 */
class DeleteItemsMiddleware implements PluginMiddleware
{
    /**
     * @var CommandEnqueuer
     *
     * Command enqueuer
     */
    private $commandEnqueuer;

    /**
     * TransformIndexMiddleware constructor.
     *
     * @param CommandEnqueuer $commandEnqueuer
     */
    public function __construct(CommandEnqueuer $commandEnqueuer)
    {
        $this->commandEnqueuer = $commandEnqueuer;
    }

    /**
     * Execute middleware.
     *
     * @param DeleteItems $command
     * @param callable    $next
     *
     * @return mixed
     */
    public function execute(
        $command,
        $next
    ) {
        $index = $command->getIndexUUID()->composeUUID();

        if (false !== strpos($index, '-plugin-language-*')) {
            return $next($command);
        }

        $indices = explode(',', $index);
        $indices = array_map(function (string $index) {
            return $index.'-plugin-language-*';
        }, $indices);

        $indices = implode(',', $indices);
        $indexUUID = IndexUUID::createById($indices);

        $this
            ->commandEnqueuer
            ->enqueueCommand(new DeleteItems(
                $command
                    ->getRepositoryReference()
                    ->changeIndex($indexUUID),
                $command->getToken(),
                $command->getItemsUUID()
            ));
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
        ];
    }
}
