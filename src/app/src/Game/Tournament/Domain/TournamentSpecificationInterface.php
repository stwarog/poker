<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;


use InvalidArgumentException;

interface TournamentSpecificationInterface
{
    /**
     * @param Tournament $tournament
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function isSatisfiedBy(Tournament $tournament): bool;
}
