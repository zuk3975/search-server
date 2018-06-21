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

namespace Apisearch\Server\Domain\CommandHandler;

use Apisearch\Server\Domain\Command\DeleteTokens;
use Apisearch\Server\Domain\Event\DomainEventWithRepositoryReference;
use Apisearch\Server\Domain\Event\TokensWereDeleted;
use Apisearch\Server\Domain\WithAppRepositoryAndEventPublisher;

/**
 * Class DeleteTokensHandler.
 */
class DeleteTokensHandler extends WithAppRepositoryAndEventPublisher
{
    /**
     * Delete token.
     *
     * @param DeleteTokens $deleteTokens
     */
    public function handle(DeleteTokens $deleteTokens)
    {
        $repositoryReference = $deleteTokens->getRepositoryReference();

        $this
            ->appRepository
            ->setRepositoryReference($repositoryReference);

        $this
            ->appRepository
            ->deleteTokens();

        $this
            ->eventPublisher
            ->publish(new DomainEventWithRepositoryReference(
                $repositoryReference,
                new TokensWereDeleted()
            ));
    }
}
