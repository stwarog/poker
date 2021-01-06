<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;


use App\Shared\Exception\NotFoundException;

interface TournamentByIdInterface
{
    /**
     * @param TournamentId $tournamentId
     *
     * @return Tournament
     * @throws NotFoundException
     */
    public function getById(TournamentId $tournamentId): Tournament;
}
