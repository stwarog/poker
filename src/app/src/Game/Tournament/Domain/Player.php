<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;

use App\Game\Chip;
use App\Game\Shared\Domain\Cards\CardCollection;
use App\Game\Shared\Domain\Table;
use Exception;
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

    public function chips(): Chip
    {
        return new Chip($this->chips);
    }

    private function lost()
    {
        $this->status = PlayerStatus::LOST;
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
}
