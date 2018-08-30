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
use Apisearch\Model\Item;
use Apisearch\Server\Domain\Command\IndexItems;
use Apisearch\Server\Domain\Plugin\PluginMiddleware;
use League\Tactician\CommandBus;

/**
 * Class IndexItemsMiddleware.
 */
class IndexItemsMiddleware implements PluginMiddleware
{
    /**
     * @var CommandBus
     *
     * Command bus
     */
    private $commandBus;

    /**
     * IndexItemsMiddleware constructor.
     *
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

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
        /*
         * We should check if this is a language specific command
         */
        if (1 === preg_match('~\w*\-plugin\-language\-\w{2}~', $command->getIndexUUID()->composeUUID())) {
            return $next($command);
        }

        /**
         * @var IndexItems
         */
        $itemsSplittedByLanguage = [
            'xx' => [],
        ];

        foreach ($command->getItems() as $item) {
            $language = $item->get('language') ?? 'xx';
            if (!isset($itemsSplittedByLanguage[$language])) {
                $itemsSplittedByLanguage[$language] = [];
            }
            $itemsSplittedByLanguage[$language][] = $item;
        }

        /*
         * If we have not found any item with language, just follow the normal
         * workflow
         */
        if (empty($itemsSplittedByLanguage)) {
            return $next($command);
        }

        foreach ($itemsSplittedByLanguage as $language => $items) {
            $this->enqueueLanguageSpecificIndexItems(
                $command,
                $items,
                $language
            );
        }
    }

    /**
     * Enqueue new command.
     *
     * @param IndexItems $command
     * @param Item[]     $items
     * @param string     $language
     */
    private function enqueueLanguageSpecificIndexItems(
        IndexItems $command,
        array $items,
        string $language
    ) {
        $this
            ->commandBus
            ->handle(
                new IndexItems(
                    $command
                        ->getRepositoryReference()
                        ->changeIndex(IndexUUID::createById($command
                                ->getIndexUUID()
                                ->composeUUID().'-plugin-language-'.$language
                        )),
                    $command->getToken(),
                    $items
                )
            );
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
        return [IndexItems::class];
    }
}
