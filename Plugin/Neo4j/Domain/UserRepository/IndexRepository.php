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

namespace Apisearch\Plugin\Neo4j\Domain\UserRepository;

use Apisearch\Server\Domain\Repository\UserRepository\IndexRepository as BaseIndexRepository;
use Apisearch\User\Interaction;

/**
 * Class IndexRepository.
 */
class IndexRepository extends Neo4jRepository implements BaseIndexRepository
{
    /**
     * Add interaction.
     *
     * @param Interaction $interaction
     */
    public function addInteraction(Interaction $interaction)
    {
        $user = $interaction->getUser();
        $userId = $user->getId();
        $itemUUID = $interaction->getItemUUID();
        $weight = $interaction->getWeight();
        $appUUIDComposed = $this->getAppUUID()->composeUUID();

        $query = "
            MERGE (:User { id: '$userId', app: '$appUUIDComposed' })-[rel:interacts]->(:Item {id: '{$itemUUID->composeUUID()}', app: '$appUUIDComposed'})
            ON CREATE SET rel.w = $weight
            ON MATCH SET rel.w = rel.w + $weight
            ";

        $this->runQuery($query);
    }

    /**
     * Remove all interactions.
     */
    public function deleteAllInteractions()
    {
        $appUUIDComposed = $this->getAppUUID()->composeUUID();
        $query = "
            MATCH (n)
            WHERE n.app = '{$appUUIDComposed}'
            DETACH DELETE n
            ";

        $this->runQuery($query);
    }
}
