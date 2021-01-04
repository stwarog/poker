<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;


use App\Shared\Exception\NotFoundException;

interface PlayerByIdInterface
{
    /**
     * @param string $playerId
     *
     * @return Player
     * @throws NotFoundException
     */
    public function getById(string $playerId): Player;
}
