<?php declare(strict_types=1);

namespace App\Game\Table\Domain;


use App\Shared\Exception\NotFoundException;

interface PlayerByIdInterface
{
    /**
     * @param PlayerId $playerId
     *
     * @return Player
     * @throws NotFoundException
     */
    public function getById(PlayerId $playerId): Player;
}
