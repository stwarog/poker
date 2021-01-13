<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;


use App\Account\Domain\AccountId;
use App\Game\Shared\Domain\Chip;
use App\Game\Shared\Domain\TableId;
use App\Game\Tournament\Event\ParticipantSignedIn;
use App\Game\Tournament\Event\TournamentCreated;
use App\Game\Tournament\Event\TournamentReadyForJoins;
use App\Game\Tournament\Event\TournamentStarted;
use App\Shared\Domain\AggregateRoot;
use App\Shared\Domain\Minutes;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use InvalidArgumentException;
use RuntimeException;

class Tournament extends AggregateRoot
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
    private int $blindsChangeInterval;

    private ?string $table = null;

    /** @var Participant[]|Collection */
    private Collection $participants;

    public function __construct(TournamentId $id, ?Rules $rules = null)
    {
        $this->id     = $id->toString();
        $this->status = TournamentStatus::PREPARATION;

        $this->participants = new ArrayCollection();

        $rules                = $rules ?? Rules::createDefaults();
        $this->minPlayerCount = $rules->getPlayerCount()->getMin();
        $this->maxPlayerCount = $rules->getPlayerCount()->getMax();

        $this->initialChipsPerPlayer = $rules->getInitialChipsPerPlayer()->getValue();
        $this->initialSmallBlind     = $rules->getInitialSmallBlind()->getValue();
        $this->initialBigBlind       = $rules->getInitialBigBlind()->getValue();
        $this->blindsChangeInterval  = $rules->getBlindsChangeInterval()->getValue();

        $this->record(TournamentCreated::createEmpty($this->id));
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
     * @param AccountId $account
     *
     * @return ParticipantId
     * @throws Exception
     */
    public function signUp(AccountId $account): ParticipantId
    {
        if (false === $this->isReadyForSignUps()) {
            throw new Exception('Tournament sign up is closed');
        }

        if ($this->hasAccount($account)) {
            throw new Exception('Account already signed up');
        }

        $rules = $this->getRules();

        $maxPlayersCount = $rules->getPlayerCount()->getMax();
        if ($hasMaxPlayersCount = $this->getParticipantCount() === $maxPlayersCount) {
            throw new InvalidArgumentException(sprintf('Tournament has already full amount of participants'));
        }

        $p = Participant::create($account);
        $this->participants->set($p->getId()->toString(), $p);

        $currentParticipantsCount = $this->participants->count();
        if ($currentParticipantsCount >= $rules->getPlayerCount()->getMin()) {
            $this->status = TournamentStatus::READY;
            $this->record(TournamentReadyForJoins::createEmpty($this->id));
        }

        $this->record(
            ParticipantSignedIn::create($this->id, $p->getId()->toString(), $account->toString())
        );

        return $p->getId();
    }

    private function isReadyForSignUps(): bool
    {
        return in_array($this->status, [TournamentStatus::SIGN_UPS, TournamentStatus::READY]);
    }

    public function getParticipantCount(): int
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
            new Minutes($this->blindsChangeInterval)
        );
    }

    public function hasParticipant(ParticipantId $participant): bool
    {
        return !$this->participants->filter(fn(Participant $p) => $p->getId()->equals($participant))->isEmpty();
    }

    public function start(TableId $table): void
    {
        if (false === $this->isReady()) {
            throw new RuntimeException('Tournament is not ready to start');
        }

        $this->status = TournamentStatus::STARTED;

        $this->table = $table->toString();

        $this->record(TournamentStarted::create($this->id, $table->toString()));
    }

    private function isReady(): bool
    {
        return $this->status === TournamentStatus::READY;
    }

    public function publish(): void
    {
        if ($isNotUnderPreparation = $this->status !== TournamentStatus::PREPARATION) {
            throw new RuntimeException('Tournament must be in preparation status to get published');
        }

        $this->status = TournamentStatus::SIGN_UPS;
    }

    public function getStatus(): TournamentStatus
    {
        return new TournamentStatus($this->status);
    }

    private function hasAccount(AccountId $account)
    {
        return !$this->participants->filter(fn(Participant $p) => $p->getAccountId()->equals($account))->isEmpty();
    }
}
