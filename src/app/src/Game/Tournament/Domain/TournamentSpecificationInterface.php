<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;


interface TournamentSpecificationInterface
{
    public function isSatisfiedBy(Tournament $tournament): bool;

    public function getReason(): string;
}
