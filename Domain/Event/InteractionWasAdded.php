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

use Apisearch\User\Interaction;

/**
 * Class InteractionWasAdded.
 */
class InteractionWasAdded extends DomainEvent
{
    /**
     * @var Interaction
     *
     * Interaction
     */
    private $interaction;

    /**
     * ItemsWasIndexed constructor.
     */
    public function __construct(Interaction $interaction)
    {
        $this->setNow();
        $this->interaction = $interaction;
    }

    /**
     * to array payload.
     *
     * @return array
     */
    public function toArrayPayload(): array
    {
        return [
            'interaction' => $this
                ->interaction
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
            Interaction::createFromArray($arrayPayload['interaction']),
        ];
    }
}
