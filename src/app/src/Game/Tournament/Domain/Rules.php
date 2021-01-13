<?php declare(strict_types=1);


namespace App\Game\Tournament\Domain;


use App\Game\Shared\Domain\Chip;
use App\Shared\Domain\Minutes;

class Rules
{
    private PlayerCount $playerCount;
    private Chip $initialChipsPerPlayer;
    private Chip $initialSmallBlind;
    private Chip $initialBigBlind;
    private Minutes $blindsChangeInterval;

    public function __construct(
        PlayerCount $playerCount,
        Chip $initialChipsPerPlayer,
        Chip $initialSmallBlind,
        Chip $initialBigBlind,
        Minutes $blindsChangeInterval
    ) {
        $this->playerCount           = $playerCount;
        $this->initialChipsPerPlayer = $initialChipsPerPlayer;
        $this->initialSmallBlind     = $initialSmallBlind;
        $this->initialBigBlind       = $initialBigBlind;
        $this->blindsChangeInterval  = $blindsChangeInterval;
    }

    public static function createDefaults(): self
    {
        return new self(
            new PlayerCount(),
            new Chip(4000),
            new Chip(25),
            new Chip(50),
            new Minutes(2)
        );
    }

    public function getPlayerCount(): PlayerCount
    {
        return $this->playerCount;
    }

    public function getInitialChipsPerPlayer(): Chip
    {
        return $this->initialChipsPerPlayer;
    }

    public function getInitialSmallBlind(): Chip
    {
        return $this->initialSmallBlind;
    }

    public function getInitialBigBlind(): Chip
    {
        return $this->initialBigBlind;
    }

    public function getBlindsChangeInterval(): Minutes
    {
        return $this->blindsChangeInterval;
    }
}
