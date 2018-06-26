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

use Apisearch\Query\Filter;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Result\Result;
use Apisearch\Server\Domain\Plugin\PluginMiddleware;
use Apisearch\Server\Domain\Query\Query;

/**
 * Class QueryMiddleware.
 */
class QueryMiddleware implements PluginMiddleware
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
        /**
         * @var Result
         * @var Query  $command
         */
        $languagesFiltered = [];
        $languagesFilter = $command
            ->getQuery()
            ->getFilterByField('language');

        if ($languagesFilter instanceof Filter) {
            $languagesFiltered = $languagesFilter->getValues();
        }

        $index = $command->getIndex();
        $indices = explode(',', $index);
        $indices = array_map(function (string $index) use ($languagesFiltered) {
            if (empty($languagesFiltered)) {
                return $index.'_plugin_language_*';
            }

            $indicesWithLanguage = [];
            foreach ($languagesFiltered as $language) {
                $indicesWithLanguage[] = $index.'_plugin_language_'.$language;
            }

            return implode(',', $indicesWithLanguage);
        }, $indices);

        $indices = implode(',', $indices);

        $command->setRepositoryReference(RepositoryReference::create(
            $command->getAppId(),
            $indices
        ));

        /*
         * @var Result
         * @var Query  $command
         */
        return $next($command);
    }

    /**
     * Events subscribed namespace.
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [Query::class];
    }
}
