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

namespace Apisearch\Server\Domain;

use Apisearch\Model\IndexUUID;
use Apisearch\Model\Token;
use Apisearch\Repository\RepositoryReference;

/**
 * Class AsynchronousCommandWithRepositoryReferenceAndTokenAndIndexUUID.
 *
 * All implementations of this class will have a basic repository reference,
 * with an autentication TokenUUID and an index.
 */
abstract class AsynchronousCommandWithRepositoryReferenceAndTokenAndIndexUUID extends CommandWithRepositoryReferenceAndTokenAndIndexUUID implements WriteCommand, AsynchronousableCommand
{
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
            'index_uuid' => $this
                ->getIndexUUID()
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
        return new static(
            RepositoryReference::createFromComposed($data['repository_reference']),
            Token::createFromArray($data['token']),
            IndexUUID::createFromArray($data['index_uuid'])
        );
    }
}
