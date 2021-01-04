<?php declare(strict_types=1);


namespace App\Game\Tournament\Domain;


class Rules
{
    private int $minPlayerCount;
    private int $maxPlayerCount;

    public function __construct(PlayerCount $playerCount)
    {
        $this->minPlayerCount = $playerCount->getMin();
        $this->maxPlayerCount = $playerCount->getMax();
    }

    public static function createDefaults(): self
    {
        return new self(
            new PlayerCount()
        );
    }

    public function getPlayerCount(): PlayerCount
    {
        return new PlayerCount($this->minPlayerCount, $this->maxPlayerCount);
    }
}
