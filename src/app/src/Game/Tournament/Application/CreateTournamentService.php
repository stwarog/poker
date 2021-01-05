<?php declare(strict_types=1);


namespace App\Game\Tournament\Application;


use App\Game\Tournament\Domain\PlayerCount;
use App\Game\Tournament\Domain\Rules;
use App\Game\Tournament\Domain\Tournament;

class CreateTournamentService
{
    public function create(PlayerCount $playerCount): Tournament
    {
        $r = new Rules($playerCount);
        $t = new Tournament($r);

        return $t;
    }
}
