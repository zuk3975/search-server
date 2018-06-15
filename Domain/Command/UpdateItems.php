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

namespace Apisearch\Server\Domain\Command;

use Apisearch\Model\Changes;
use Apisearch\Query\Query;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Repository\WithRepositoryReference;
use Apisearch\Server\Domain\AsynchronousableCommand;
use Apisearch\Server\Domain\CommandWithRepositoryReferenceAndToken;
use Apisearch\Server\Domain\IndexRequiredCommand;
use Apisearch\Server\Domain\LoggableCommand;
use Apisearch\Server\Domain\WriteCommand;
use Apisearch\Token\Token;

/**
 * Class UpdateItems.
 */
class UpdateItems extends CommandWithRepositoryReferenceAndToken implements WithRepositoryReference, WriteCommand, LoggableCommand, AsynchronousableCommand, IndexRequiredCommand
{
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
        parent::__construct(
            $repositoryReference,
            $token
        );

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

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'query' => $this
                ->query
                ->toArray(),
            'changes' => $this
                ->changes
                ->toArray(),
            'repository_reference' => [
                'app_id' => $this->getRepositoryReference()->getAppId(),
                'index' => $this->getRepositoryReference()->getIndex(),
            ],
            'token' => $this->getToken()->toArray(),
        ];
    }

    /**
     * Create command from array.
     *
     * @param array $data
     *
     * @return AsynchronousableCommand
     */
    public static function fromArray(array $data): AsynchronousableCommand
    {
        return new self(
            RepositoryReference::create(
                $data['repository_reference']['app_id'],
                $data['repository_reference']['index']
            ),
            Token::createFromArray($data['token']),
            Query::createFromArray($data['query']),
            Changes::createFromArray($data['changes'])
        );
    }
}
