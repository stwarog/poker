<?php declare(strict_types=1);


namespace App\Game\Tournament\Application;


use App\Game\Shared\Domain\Cards\Card;
use App\Game\Shared\Domain\Cards\CardCollection;

class TableView
{
    public int $round;
    public CardCollection $cards;
    public int $chips;
    public int $smallBlind;
    public int $bigBlind;
    public string $player;

    public function __construct(
        int $round,
        CardCollection $cards,
        int $chips,
        int $smallBlind,
        int $bigBlind,
        string $player
    ) {
        $this->round      = $round;
        $this->cards      = $cards;
        $this->chips      = $chips;
        $this->smallBlind = $smallBlind;
        $this->bigBlind   = $bigBlind;
        $this->player     = $player;
    }

    public static function fromArray(array $array): self
    {
        return new self(
            (int) $array['round'],
            unserialize($array['cards']),
            (int) $array['chips'],
            (int) $array['small_blind'],
            (int) $array['big_blind'],
            $array['player_id'],
        );
    }

    public function toArray(): array
    {
        return [
            'round'       => $this->round,
            'cards'       => array_map(fn(Card $card) => (string) $card, $this->cards->toArray()),
            'chips'       => $this->chips,
            'small_blind' => $this->smallBlind,
            'big_blind'   => $this->bigBlind,
            'player_id'   => $this->player,
        ];
    }
}
