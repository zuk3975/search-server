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

use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\AppRequiredCommand;
use Apisearch\Server\Domain\AsynchronousableCommand;
use Apisearch\Server\Domain\CommandWithRepositoryReferenceAndToken;
use Apisearch\Server\Domain\LoggableCommand;
use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;

/**
 * Class DeleteToken.
 */
class DeleteToken extends CommandWithRepositoryReferenceAndToken implements LoggableCommand, AsynchronousableCommand, AppRequiredCommand
{
    /**
     * @var TokenUUID
     *
     * Token UUID
     */
    private $tokenUUID;

    /**
     * AddToken constructor.
     *
     *
     * @param RepositoryReference $repositoryReference
     * @param Token               $token
     * @param TokenUUID           $tokenUUID
     */
    public function __construct(
        RepositoryReference $repositoryReference,
        Token              $token,
        TokenUUID $tokenUUID
    ) {
        parent::__construct(
            $repositoryReference,
            $token
        );

        $this->tokenUUID = $tokenUUID;
    }

    /**
     * Get Token.
     *
     * @return TokenUUID
     */
    public function getTokenUUID(): TokenUUID
    {
        return $this->tokenUUID;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'token_uuid' => $this
                ->tokenUUID
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
            TokenUUID::createFromArray($data['token_uuid'])
        );
    }
}
