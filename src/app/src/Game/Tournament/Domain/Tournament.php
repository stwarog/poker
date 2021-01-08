<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;


use App\Game\Chip;
use App\Game\Shared\Domain\Table;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use InvalidArgumentException;
use RuntimeException;

class Tournament
{
    private string $id;
    private string $status;

    # player count
    private int $minPlayerCount;
    private int $maxPlayerCount;

    # chips
    private int $initialChipsPerPlayer;
    private int $initialSmallBlind;
    private int $initialBigBlind;

    /** @var Player[]|Collection */
    private Collection $participants; # signed up
    /** @var Player[]|Collection */
    private Collection $players; # joined to game

    private ?Table $table = null;

    public function __construct(
        ?TournamentId $id = null,
        ?Rules $rules = null
    ) {
        $this->id     = $id ? (string) $id : (string) TournamentId::create();
        $this->status = TournamentStatus::PREPARATION;

        $this->participants = new ArrayCollection();
        $this->players      = new ArrayCollection();

        $rules                = $rules ?? Rules::createDefaults();
        $this->minPlayerCount = $rules->getPlayerCount()->getMin();
        $this->maxPlayerCount = $rules->getPlayerCount()->getMax();

        $this->initialChipsPerPlayer = $rules->getInitialChipsPerPlayer()->getValue();
        $this->initialSmallBlind     = $rules->getInitialSmallBlind()->getValue();
        $this->initialBigBlind       = $rules->getInitialBigBlind()->getValue();
    }

    public static function create(?Rules $rules = null): self
    {
        return new self(TournamentId::create(), $rules);
    }

    public function getId(): TournamentId
    {
        return TournamentId::fromString($this->id);
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

        if ($hasMaxPlayersCount = $this->participantCount() === $this->getRules()->getPlayerCount()->getMax()) {
            throw new InvalidArgumentException(sprintf('Tournament has already full amount of participants'));
        }

        $p = new Player();
        $r = $this->getRules();
        $p->addChips($r->getInitialChipsPerPlayer());
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

    public function getRules(): Rules
    {
        return new Rules(
            new PlayerCount($this->minPlayerCount, $this->maxPlayerCount),
            new Chip($this->initialChipsPerPlayer),
            new Chip($this->initialSmallBlind),
            new Chip($this->initialBigBlind),
        );
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

        if ($isReadyToStart = $this->getPlayersCount() >= $this->getRules()->getPlayerCount()->getMin()) {
            $this->status = TournamentStatus::READY;
        }
    }

    public function hasParticipant(PlayerId $participant): bool
    {
        return $this->participants->containsKey($participant->toString());
    }

    public function hasPlayer(PlayerId $player): bool
    {
        return $this->players->containsKey($player->toString());
    }

    public function getPlayersCount(): int
    {
        return $this->players->count();
    }

    /**
     * @param Table $table
     *
     * @throws Exception
     */
    public function start(Table $table): void
    {
        if (false === $this->isReady()) {
            throw new RuntimeException('Tournament is not ready to start');
        }

        $this->status = TournamentStatus::STARTED;

        $this->table = $table;

        $players = $this->getPlayers();

        $players[0]->giveSmallBlind($table);
        $players[1]->giveBigBlind($table);

        $nextPlayer = $this->getNextPlayer();
        $this->table->setCurrentPlayer($nextPlayer);

        foreach ($players as $player) {
            $player->pickCards($table, 2);
        }

        $table->revealCards(3);
    }

    private function isReady(): bool
    {
        return $this->status === TournamentStatus::READY;
    }

    /**
     * @return Player[]
     */
    public function getPlayers(): array
    {
        return array_values($this->players->toArray());
    }

    private function getNextPlayer(): Player
    {
        $players     = array_values($this->players->toArray());
        $hasBigBlind = array_filter($players, fn(Player $p) => $p->hasBigBlind());
        if (empty($hasBigBlind)) {
            throw new RuntimeException('Attempted to get next player, but no Big Blind assigned');
        }
        $index = array_key_first($hasBigBlind);
        $index++;

        return isset($players[$index]) ? $players[$index] : $players[0];
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

    public function getPlayerChips(PlayerId $p): Chip
    {
        return $this->players->get($p->toString())->chips();
    }

    public function getCurrentPlayer(): PlayerId
    {
        return $this->table->getCurrentPlayer();
    }
}
