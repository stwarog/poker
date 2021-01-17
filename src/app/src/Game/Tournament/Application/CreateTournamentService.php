<?php declare(strict_types=1);


namespace App\Game\Tournament\Application;


use App\Game\Shared\Domain\Chip;
use App\Game\Tournament\Domain\PlayerCount;
use App\Game\Tournament\Domain\Rules;
use App\Game\Tournament\Domain\Tournament;
use App\Game\Tournament\Domain\TournamentId;
use App\Game\Tournament\Domain\TournamentRepositoryInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Minutes;

class CreateTournamentService
{
    private TournamentRepositoryInterface $repository;
    private EventBusInterface $bus;

    public function __construct(TournamentRepositoryInterface $repository, EventBusInterface $bus)
    {
        $this->repository = $repository;
        $this->bus = $bus;
    }

    public function create(
        PlayerCount $playerCount,
        Chip $chipsPerPlayer,
        Chip $initialSmallBlind,
        Chip $initialBigBlind,
        Minutes $blindsChangeInterval,
        bool $publish
    ): TournamentId {
        $r = new Rules($playerCount, $chipsPerPlayer, $initialSmallBlind, $initialBigBlind, $blindsChangeInterval);
        $t = Tournament::create($r);

        if ($publish) {
            $t->publish();
        }

        $this->repository->save($t);

        $this->bus->publish(...$t->pullDomainEvents());

        return $t->getId();
    }
}
