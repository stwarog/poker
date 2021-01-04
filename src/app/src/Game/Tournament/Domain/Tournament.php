<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;


use RuntimeException;

class Tournament
{
    private TournamentStatus $status;

    /** @var Player[] */
    private array $players = [];

    public function __construct(?TournamentStatus $status = null)
    {
        $this->status = $status ?? TournamentStatus::PENDING();
    }

    public function getStatus(): TournamentStatus
    {
        return $this->status;
    }

    public function playersCount(): int
    {
        return count($this->players);
    }

    public function signUp(Player $player, TournamentSpecificationInterface $joinSpecification): void
    {
        if (false === $joinSpecification->isSatisfiedBy($this)) {
            throw new RuntimeException('Tournament is not playable yet');
        }

        if ($this->hasPlayer($player)) {
            throw new RuntimeException('Player already registered to this tournament');
        }

        $this->players[] = $player;
    }

    public function hasPlayer(Player $player): bool
    {
        return !empty(array_filter($this->players, fn(Player $p) => $p->getId()->equals($player->getId())));
    }
}
