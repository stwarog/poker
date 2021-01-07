<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;

use App\Game\Chip;
use App\Game\Shared\Domain\Cards\CardCollection;
use RuntimeException;
use Webmozart\Assert\Assert;

class Player
{
    private string $id;
    private string $status = PlayerStatus::ACTIVE;
    private int $chips = 0;
    private CardCollection $cards;
    private string $role = PlayerRole::NONE;

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
        $current   = $this->chipsAmount()->getValue();
        $requested = $amount->getValue();

        Assert::greaterThan($requested, 0, 'Can not take 0 value chip');

        Assert::greaterThanEq($current - $requested, 0, sprintf('Requested to take %d chips but user has only %d', $requested, $current));
        $this->chips -= $amount->getValue();

        if ($this->chips === 0) {
            $this->lost();
        }
    }

    public function chipsAmount(): Chip
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

    public function pickCards(Tournament $t, int $amount): void
    {
        foreach ($t->deck()->pickCard($amount) as $card) {
            $this->cards->addCard($card);
        }
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

    public function giveSmallBlind(Tournament $tournament)
    {
        if ($this->role !== PlayerRole::NONE) {
            throw new RuntimeException('Player can not have any role to give small blind');
        }
        $this->role  = PlayerRole::SMALL_BLIND;
        $this->chips = $this->chipsAmount()->take($tournament->currentSmallBlind())->getValue();
    }

    public function giveBigBlind(Tournament $tournament)
    {
        if ($this->role !== PlayerRole::NONE) {
            throw new RuntimeException('Player can not have any role to give big blind');
        }
        $this->role  = PlayerRole::BIG_BLIND;
        $this->chips = $this->chipsAmount()->take($tournament->currentBigBlind())->getValue();
    }
}
