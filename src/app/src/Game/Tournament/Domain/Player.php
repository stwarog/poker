<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;

use App\Game\Chip;
use App\Game\Shared\Domain\Cards\CardCollection;
use App\Game\Shared\Domain\Table;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Webmozart\Assert\Assert;

class Player
{
    private string $id;
    private string $status = PlayerStatus::ACTIVE;
    private int $chips = 0;
    private CardCollection $cards;
    private string $role = PlayerRole::NONE;
    private bool $hasTurn = false;
    private int $currentBet = 0;

    public function __construct(?PlayerId $uuid = null)
    {
        $this->id    = $uuid ? (string) $uuid : (string) PlayerId::create();
        $this->cards = new CardCollection();
    }

    public function getId(): PlayerId
    {
        return PlayerId::fromString($this->id);
    }

    public function addChips(Chip $amount): void
    {
        Assert::greaterThan($amount->getValue(), 0, 'Can not add 0 value chips');
        $this->chips += $amount->getValue();
    }

    public function getStatus(): PlayerStatus
    {
        return new PlayerStatus($this->status);
    }

    public function pickCards(Table $t, int $amount): void
    {
        $this->cards->addCards(
            $t->pickCard($amount)
        );
    }

    public function getCards(): CardCollection
    {
        return $this->cards;
    }

    public function hasSmallBlind(): bool
    {
        return $this->role === PlayerRole::SMALL_BLIND;
    }

    public function hasBigBlind(): bool
    {
        return $this->role === PlayerRole::BIG_BLIND;
    }

    public function giveSmallBlind(Table $table): void
    {
        if ($this->role !== PlayerRole::NONE) {
            throw new RuntimeException('Player can not have any role to give small blind');
        }
        $this->role  = PlayerRole::SMALL_BLIND;
        $amount      = $table->currentSmallBlind();
        $this->chips = $this->chips()->take($amount)->getValue();
        $table->putChips($amount);
    }

    public function chips(): Chip
    {
        return new Chip($this->chips);
    }

    public function giveBigBlind(Table $table): void
    {
        if ($this->role !== PlayerRole::NONE) {
            throw new RuntimeException('Player can not have any role to give big blind');
        }
        $this->role  = PlayerRole::BIG_BLIND;
        $amount      = $table->currentBigBlind();
        $this->chips = $this->chips()->take($amount)->getValue();
        $table->putChips($amount);
    }

    /**
     * @throws Exception
     */
    public function turn(): void
    {
        if ($this->hasTurn()) {
            throw new Exception('Already has turn');
        }
        $this->hasTurn = true;
    }

    public function hasTurn(): bool
    {
        return $this->hasTurn;
    }

    /**
     * @param Table $table
     *
     * @throws Exception
     */
    public function fold(Table $table): void
    {
        if (false === $this->hasTurn()) {
            throw new Exception('Not this player turn');
        }

        $this->hasTurn = false;
        $table->nextPlayer();
    }

    /**
     * @param Table $table
     *
     * @throws Exception
     */
    public function call(Table $table): void
    {
        if (false === $this->hasTurn()) {
            throw new Exception('Not this player turn');
        }

        $currentBet = $table->getCurrentBet();
        $this->takeChips($currentBet);
        $this->currentBet = $currentBet->getValue();
        $table->putChips($currentBet);


        $this->hasTurn = false;
        $table->nextPlayer();
    }

    public function takeChips(Chip $amount): void
    {
        $current   = $this->chips()->getValue();
        $requested = $amount->getValue();

        Assert::greaterThan($requested, 0, 'Can not take 0 value chip');

        Assert::greaterThanEq($current - $requested, 0, sprintf('Requested to take %d chips but user has only %d', $requested, $current));
        $this->chips -= $amount->getValue();

        if ($this->chips === 0) {
            $this->lost();
        }
    }

    private function lost()
    {
        $this->status = PlayerStatus::LOST;
    }

    /**
     * @param Table $table
     *
     * @throws Exception
     */
    public function allIn(Table $table): void
    {
        $playerChips = $this->chips();
        $this->raise($table, $playerChips);
    }

    /**
     * @param Table $table
     * @param Chip  $amount
     *
     * @throws Exception
     */
    public function raise(Table $table, Chip $amount): void
    {
        if (false === $this->hasTurn()) {
            throw new Exception('Not this player turn');
        }

        $doubleBigBlindValue = $table->currentBigBlind()->getValue() * 2;
        if ($amount->getValue() < $doubleBigBlindValue) {
            throw new InvalidArgumentException('Bet must be at least twice big blind value');
        }

        $this->takeChips($amount);
        $this->currentBet = $amount->getValue();
        $table->putChips($amount);

        $this->hasTurn = false;
        $table->nextPlayer();
    }

    public function getCurrentBet(): Chip
    {
        return new Chip($this->currentBet);
    }
}
