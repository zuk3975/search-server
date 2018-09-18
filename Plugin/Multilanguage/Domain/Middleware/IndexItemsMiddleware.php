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
use Apisearch\Server\Domain\CommandEnqueuer\CommandEnqueuer;
use Apisearch\Server\Domain\Plugin\PluginMiddleware;

/**
 * Class IndexItemsMiddleware.
 */
class IndexItemsMiddleware implements PluginMiddleware
{
    /**
     * @var CommandEnqueuer
     *
     * Command enqueuer
     */
    private $commandEnqueuer;

    /**
     * @var string
     *
     * Language field
     */
    private $languageField;

    /**
     * IndexItemsMiddleware constructor.
     *
     * @param CommandEnqueuer $commandEnqueuer
     * @param string          $languageField
     */
    public function __construct(
        CommandEnqueuer $commandEnqueuer,
        string $languageField
    ) {
        $this->commandEnqueuer = $commandEnqueuer;
        $this->languageField = $languageField;
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
            $language = $item->get($this->languageField) ?? 'xx';
            if (!isset($itemsSplittedByLanguage[$language])) {
                $itemsSplittedByLanguage[$language] = [];
            }
            $itemsSplittedByLanguage[$language][] = $item;
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
            ->commandEnqueuer
            ->enqueueCommand(
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
