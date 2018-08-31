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

use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\AppRequiredCommand;
use Apisearch\Server\Domain\AsynchronousableCommand;
use Apisearch\Server\Domain\CommandWithRepositoryReferenceAndToken;
use Apisearch\Server\Domain\LoggableCommand;

/**
 * Class DeleteToken.
 */
class DeleteToken extends CommandWithRepositoryReferenceAndToken implements LoggableCommand, AsynchronousableCommand, AppRequiredCommand
{
    /**
     * @var TokenUUID
     *
     * TokenUUID to delete
     */
    private $tokenUUIDToDelete;

    /**
     * AddToken constructor.
     *
     *
     * @param RepositoryReference $repositoryReference
     * @param Token               $token
     * @param TokenUUID           $tokenUUIDToDelete
     */
    public function __construct(
        RepositoryReference $repositoryReference,
        Token              $token,
        TokenUUID $tokenUUIDToDelete
    ) {
        parent::__construct(
            $repositoryReference,
            $token
        );

        $this->tokenUUIDToDelete = $tokenUUIDToDelete;
    }

    /**
     * Get Token to delete.
     *
     * @return TokenUUID
     */
    public function getTokenUUIDToDelete(): TokenUUID
    {
        return $this->tokenUUIDToDelete;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'repository_reference' => $this
                ->getRepositoryReference()
                ->compose(),
            'token' => $this
                ->getToken()
                ->toArray(),
            'token_uuid_to_delete' => $this
                ->getTokenUUIDToDelete()
                ->toArray(),
        ];
    }

    /**
     * Create command from array.
     *
     * @param array $data
     *
     * @return self
     */
    public static function fromArray(array $data)
    {
        return new self(
            RepositoryReference::createFromComposed($data['repository_reference']),
            Token::createFromArray($data['token']),
            TokenUUID::createFromArray($data['token_uuid_to_delete'])
        );
    }
}
