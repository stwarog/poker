<?php declare(strict_types=1);


namespace App\Game\Tournament\Domain;


use App\Game\Chip;

class Rules
{
    private PlayerCount $playerCount;
    private Chip $initialChipsPerPlayer;
    private Chip $initialSmallBlind;
    private Chip $initialBigBlind;

    public function __construct(
        PlayerCount $playerCount,
        Chip $initialChipsPerPlayer,
        Chip $initialSmallBlind,
        Chip $initialBigBlind
    ) {
        $this->playerCount           = $playerCount;
        $this->initialChipsPerPlayer = $initialChipsPerPlayer;
        $this->initialSmallBlind     = $initialSmallBlind;
        $this->initialBigBlind       = $initialBigBlind;
    }

    public static function createDefaults(): self
    {
        return new self(
            new PlayerCount(),
            new Chip(4000),
            new Chip(25),
            new Chip(50),
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
}
