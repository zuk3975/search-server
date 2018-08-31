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

use Apisearch\Model\Token;
use Apisearch\Repository\RepositoryReference;

/**
 * Class AsynchronousRepositoryReferenceAndToken.
 */
trait AsynchronousRepositoryReferenceAndToken
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
        ];
    }

    /**
     * Create command from array.
     *
     * @param array $data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            RepositoryReference::createFromComposed($data['repository_reference']),
            Token::createFromArray($data['token'])
        );
    }
}
