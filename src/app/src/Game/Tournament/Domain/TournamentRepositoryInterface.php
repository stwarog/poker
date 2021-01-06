<?php declare(strict_types=1);


namespace App\Game\Tournament\Domain;


interface TournamentRepositoryInterface extends TournamentByIdInterface
{
    public function save(Tournament $tournament): void;
}
