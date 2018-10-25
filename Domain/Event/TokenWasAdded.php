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

namespace Apisearch\Server\Domain\Event;

use Apisearch\Model\Token;

/**
 * Class TokenWasAdded.
 */
class TokenWasAdded extends DomainEvent
{
    /**
     * @var Token
     *
     * Token
     */
    private $token;

    /**
     * ItemsWasIndexed constructor.
     */
    public function __construct(Token $token)
    {
        $this->setNow();
        $this->token = $token;
    }

    /**
     * to array payload.
     *
     * @return array
     */
    public function toArrayPayload(): array
    {
        return [
            'token' => $this
                ->token
                ->toArray(),
        ];
    }

    /**
     * To payload.
     *
     * @param array $arrayPayload
     *
     * @return array
     */
    public static function fromArrayPayload(array $arrayPayload): array
    {
        return [
            Token::createFromArray($arrayPayload['token']),
        ];
    }
}
