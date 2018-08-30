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

use Apisearch\Model\Index;
use Apisearch\Server\Domain\Query\GetIndices;
use Apisearch\Server\Domain\WithAppRepository;

/**
 * Class GetIndicesHandler.
 */
class GetIndicesHandler extends WithAppRepository
{
    /**
     * Get indices handler method.
     *
     * @param GetIndices $getIndices
     *
     * @return Index[]
     */
    public function handle(GetIndices $getIndices): array
    {
        $repositoryReference = $getIndices->getRepositoryReference();
        $this
            ->appRepository
            ->setRepositoryReference($repositoryReference);

        return $this
            ->appRepository
            ->getIndices();
    }
}
