<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use InvalidArgumentException;
use RuntimeException;

class Tournament
{
    private string $id;
    private string $status;

    /** @var Player[]|Collection */
    private Collection $participants; # signed up
    /** @var Player[]|Collection */
    private Collection $players; # joined to game

    private Rules $rules;

    public function __construct(
        ?TournamentId $id = null,
        ?Rules $rules = null
    ) {
        $this->id           = $id ? (string) $id : (string) TournamentId::create();
        $this->status       = TournamentStatus::PREPARATION;
        $this->rules        = $rules ?? Rules::createDefaults();
        $this->participants = new ArrayCollection();
        $this->players      = new ArrayCollection();
    }

    public static function create(?Rules $rules = null): self
    {
        return new self(TournamentId::create(), $rules);
    }

    /**
     * @param Player $player
     *
     * @throws Exception
     */
    public function signUp(Player $player): void
    {
        if (false === $this->isReadyForSignUps()) {
            throw new Exception('Tournament sign up is closed');
        }

        if ($hasMaxPlayersCount = $this->participantCount() === $this->rules->getPlayerCount()->getMax()) {
            throw new InvalidArgumentException(sprintf('Tournament has already full amount of participants'));
        }

        if ($this->hasParticipant($player)) {
            throw new RuntimeException('Participant already registered to this tournament');
        }

        $this->participants[] = $player;
    }

    private function isReadyForSignUps(): bool
    {
        return $this->status === TournamentStatus::SIGN_UPS;
    }

    public function participantCount(): int
    {
        return count($this->participants);
    }

    private function hasParticipant(Player $player): bool
    {
        return !empty(array_filter($this->participants, fn(Player $p) => $p->getId()->equals($player->getId())));
    }

    public function startTournament(): void
    {
        $this->status = TournamentStatus::STARTED;
    }

    /**
     * @param Player $player
     *
     * @throws RuntimeException
     */
    public function join(Player $player): void
    {
        if (false === $this->hasParticipant($player)) {
            throw new RuntimeException('Can not join this tournament because is not signed up');
        }

        if ($this->hasPlayer($player)) {
            throw new RuntimeException('Player already joined to this tournament');
        }

        $this->players[] = $player;

        if ($isReadyToStart = $this->getPlayersCount() >= $this->rules->getPlayerCount()->getMin()) {
            $this->status = TournamentStatus::READY;
        }
    }

    public function hasPlayer(Player $player): bool
    {
        return !empty(array_filter($this->players, fn(Player $p) => $p->getId()->equals($player->getId())));
    }

    private function getPlayersCount(): int
    {
        return count($this->players);
    }

    public function start(): void
    {
        if (false === $this->isReady()) {
            throw new RuntimeException('Tournament is not ready to start');
        }
    }

    private function isReady(): bool
    {
        return $this->status === TournamentStatus::READY;
    }

    public function leave(Player $player): void
    {
        if (false === $this->hasPlayer($player)) {
            throw new InvalidArgumentException('Player is already out of this tournament');
        }

        $this->players = array_filter($this->players, fn(Player $p) => $p->getId()->notEquals($player->getId()));
    }

    public function publish(): void
    {
        if ($isNotUnderPreparation = false === $this->getStatus()->equals(TournamentStatus::PREPARATION())) {
            throw new RuntimeException('Tournament must be in preparation status to get published');
        }

        $this->status = TournamentStatus::SIGN_UPS;
    }

    public function getStatus(): TournamentStatus
    {
        return new TournamentStatus($this->status);
    }
}
