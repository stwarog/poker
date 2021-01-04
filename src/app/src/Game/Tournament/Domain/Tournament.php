<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;


use Exception;
use InvalidArgumentException;
use RuntimeException;

class Tournament
{
    private TournamentStatus $status;

    /** @var Player[] */
    private array $players = [];
    private Rules $rules;

    public function __construct(?Rules $rules = null)
    {
        $this->status = TournamentStatus::PENDING();
        $this->rules  = $rules ?? Rules::createDefaults();
    }

    public function getStatus(): TournamentStatus
    {
        return $this->status;
    }

    public function playersCount(): int
    {
        return count($this->players);
    }

    /**
     * @param Player $player
     *
     * @throws Exception
     */
    public function signUp(Player $player): void
    {
        $isPendingOrReady = $this->isReady() || $this->isPending();

        if (false === $isPendingOrReady) {
            throw new Exception('Tournament sign up is closed');
        }

        if ($hasMaxPlayersCount = $this->playersCount() === $this->rules->getPlayerCount()->getMax()) {
            throw new InvalidArgumentException(sprintf('Tournament has already full amount of players'));
        }

        if ($this->hasPlayer($player)) {
            throw new RuntimeException('Player already registered to this tournament');
        }

        $this->players[] = $player;

        if ($isReadyToStart = $this->playersCount() >= $this->rules->getPlayerCount()->getMin()) {
            $this->status = TournamentStatus::READY();
        }
    }

    private function hasPlayer(Player $player): bool
    {
        return !empty(array_filter($this->players, fn(Player $p) => $p->getId()->equals($player->getId())));
    }

    public function startTournament(): void
    {
        $this->status = TournamentStatus::STARTED();
    }

    private function isPending(): bool
    {
        return $this->status->equals(TournamentStatus::PENDING());
    }

    private function isReady(): bool
    {
        return $this->status->equals(TournamentStatus::READY());
    }
}
