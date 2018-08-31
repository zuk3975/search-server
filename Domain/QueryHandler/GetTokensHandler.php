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

namespace Apisearch\Server\Domain\QueryHandler;

use Apisearch\Model\Token;
use Apisearch\Server\Domain\Query\GetTokens;
use Apisearch\Server\Domain\WithAppRepositoryAndEventPublisher;

/**
 * Class GetTokensHandler.
 */
class GetTokensHandler extends WithAppRepositoryAndEventPublisher
{
    /**
     * Query events.
     *
     * @param GetTokens $getTokens
     *
     * @return Token[]
     */
    public function handle(GetTokens $getTokens): array
    {
        $this
            ->appRepository
            ->setRepositoryReference(
                $getTokens->getRepositoryReference()
            );

        return $this
            ->appRepository
            ->getTokens();
    }
}
