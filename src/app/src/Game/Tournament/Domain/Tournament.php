<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;


use Exception;
use InvalidArgumentException;
use RuntimeException;

class Tournament
{
    private TournamentStatus $status;

    /** @var Player[] */
    private array $participants = []; # signed up
    /** @var Player[] */
    private array $players = []; # joined to game

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

    public function participantCount(): int
    {
        return count($this->participants);
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

        if ($hasMaxPlayersCount = $this->participantCount() === $this->rules->getPlayerCount()->getMax()) {
            throw new InvalidArgumentException(sprintf('Tournament has already full amount of participants'));
        }

        if ($this->hasParticipant($player)) {
            throw new RuntimeException('Participant already registered to this tournament');
        }

        $this->participants[] = $player;

        if ($isReadyToStart = $this->participantCount() >= $this->rules->getPlayerCount()->getMin()) {
            $this->status = TournamentStatus::READY();
        }
    }

    private function hasParticipant(Player $player): bool
    {
        return !empty(array_filter($this->participants, fn(Player $p) => $p->getId()->equals($player->getId())));
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

    /**
     * @param Player $player
     * @throws RuntimeException
     */
    public function join(Player $player): void
    {
        if (false === $this->hasParticipant($player)) {
            throw new RuntimeException('Can not join this tournament because is not signed up');
        }

        if (false === $this->isReady()) {
            throw new RuntimeException('Tournament is not ready to play');
        }

        if ($this->hasPlayer($player)) {
            throw new RuntimeException('Player already joined to this tournament');
        }

        $this->players[] = $player;
    }

    private function hasPlayer(Player $player): bool
    {
        return !empty(array_filter($this->players, fn(Player $p) => $p->getId()->equals($player->getId())));
    }
}
