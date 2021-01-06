<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;

use App\Game\Chip;
use Webmozart\Assert\Assert;

class Player
{
    private string $id;
    private int $chips = 0;
    private string $status = PlayerStatus::ACTIVE;

    public function __construct(?PlayerId $uuid = null)
    {
        $this->id = $uuid ? (string) $uuid : (string) PlayerId::create();
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

    public function getStatus(): PlayerStatus
    {
        return new PlayerStatus($this->status);
    }

    private function lost()
    {
        $this->status = PlayerStatus::LOST;
    }
}
