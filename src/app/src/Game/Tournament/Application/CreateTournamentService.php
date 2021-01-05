<?php declare(strict_types=1);


namespace App\Game\Tournament\Application;


use App\Game\Tournament\Domain\PlayerCount;
use App\Game\Tournament\Domain\Rules;
use App\Game\Tournament\Domain\Tournament;
use App\Game\Tournament\Domain\TournamentRepositoryInterface;

class CreateTournamentService
{
    private TournamentRepositoryInterface $repository;

    public function __construct(TournamentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(PlayerCount $playerCount, bool $publish): Tournament
    {
        $r = new Rules($playerCount);
        $t = Tournament::create($r);

        if ($publish) {
            $t->publish();
        }

        $this->repository->save($t);

        return $t;
    }
}
