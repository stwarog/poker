<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;


use App\Shared\Exception\NotFoundException;

interface TournamentByIdInterface
{
    /**
     * @param string $tournamentId
     *
     * @return Tournament
     * @throws NotFoundException
     */
    public function getById(string $tournamentId): Tournament;
}
