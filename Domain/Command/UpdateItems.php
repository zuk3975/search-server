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
 * @author PuntMig Technologies
 */

declare(strict_types=1);

namespace Apisearch\Server\Domain\Command;

use Apisearch\Model\Changes;
use Apisearch\Query\Query;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Repository\WithRepositoryReference;
use Apisearch\Repository\WithRepositoryReferenceTrait;
use Apisearch\Repository\WithTokenTrait;
use Apisearch\Server\Domain\LoggableCommand;
use Apisearch\Server\Domain\WriteCommand;
use Apisearch\Token\Token;

/**
 * Class UpdateItems.
 */
class UpdateItems implements WithRepositoryReference, WriteCommand, LoggableCommand
{
    use WithRepositoryReferenceTrait;
    use WithTokenTrait;

    /**
     * @var Query
     *
     * Query
     */
    private $query;

    /**
     * @var Changes
     *
     * Changes
     */
    private $changes;

    /**
     * IndexCommand constructor.
     *
     * @param RepositoryReference $repositoryReference
     * @param Token               $token
     * @param Query               $query
     * @param Changes             $changes
     */
    public function __construct(
        RepositoryReference $repositoryReference,
        Token $token,
        Query $query,
        Changes $changes
    ) {
        $this->repositoryReference = $repositoryReference;
        $this->token = $token;
        $this->query = $query;
        $this->changes = $changes;
    }

    /**
     * Get query.
     *
     * @return Query
     */
    public function getQuery(): Query
    {
        return $this->query;
    }

    /**
     * Get changes.
     *
     * @return Changes
     */
    public function getChanges(): Changes
    {
        return $this->changes;
    }
}
