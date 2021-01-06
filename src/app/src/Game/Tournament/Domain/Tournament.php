<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;


use App\Game\Chip;
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

    public function getId(): TournamentId
    {
        return TournamentId::fromString($this->id);
    }

    public static function create(?Rules $rules = null): self
    {
        return new self(TournamentId::create(), $rules);
    }

    /**
     * @return PlayerId
     * @throws Exception
     */
    public function signUp(): PlayerId
    {
        if (false === $this->isReadyForSignUps()) {
            throw new Exception('Tournament sign up is closed');
        }

        if ($hasMaxPlayersCount = $this->participantCount() === $this->rules->getPlayerCount()->getMax()) {
            throw new InvalidArgumentException(sprintf('Tournament has already full amount of participants'));
        }

        $p = new Player();
        $p->addChips($this->getRules()->getInitialChipsPerPlayer());
        $this->participants->set($p->getId()->toString(), $p);

        return $p->getId();
    }

    private function isReadyForSignUps(): bool
    {
        return $this->status === TournamentStatus::SIGN_UPS;
    }

    public function participantCount(): int
    {
        return $this->participants->count();
    }

    public function hasParticipant(PlayerId $participant): bool
    {
        return $this->participants->containsKey($participant->toString());
    }

    public function startTournament(): void
    {
        $this->status = TournamentStatus::STARTED;
    }

    /**
     * @param PlayerId $player
     *
     * @throws RuntimeException
     */
    public function join(PlayerId $player): void
    {
        if (false === $this->hasParticipant($player)) {
            throw new RuntimeException('Can not join this tournament because is not signed up');
        }

        if ($this->hasPlayer($player)) {
            throw new RuntimeException('Player already joined to this tournament');
        }

        $p = $this->participants->get($player->toString());
        $this->players->set($p->getId()->toString(), $p);

        if ($isReadyToStart = $this->getPlayersCount() >= $this->rules->getPlayerCount()->getMin()) {
            $this->status = TournamentStatus::READY;
        }
    }

    public function hasPlayer(PlayerId $player): bool
    {
        return $this->players->containsKey($player->toString());
    }

    public function getPlayersCount(): int
    {
        return $this->players->count();
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

    public function leave(PlayerId $player): void
    {
        if (false === $this->hasPlayer($player)) {
            throw new InvalidArgumentException('Player is already out of this tournament');
        }

        $this->players->remove($player->toString());
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

    private function getRules(): Rules
    {
        return $this->rules;
    }

    public function getPlayerChips(PlayerId $p): Chip
    {
        return $this->players->get($p->toString())->chipsAmount();
    }
}
