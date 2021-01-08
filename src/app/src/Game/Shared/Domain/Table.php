<?php declare(strict_types=1);


namespace App\Game\Shared\Domain;


use App\Game\Chip;
use App\Game\Shared\Domain\Cards\CardCollection;
use App\Game\Tournament\Domain\Player;
use App\Game\Tournament\Domain\PlayerId;
use App\Game\Tournament\Domain\Rules;
use Exception;

class Table
{
    private string $id;
    private int $round = 1;
    private CardCollection $deck;
    private CardCollection $cards;
    private int $chips = 0;
    private ?string $player = null;
    private int $smallBlind = 0;
    private int $bigBlind = 0;

    public function __construct(TableId $id, CardCollection $deck, Rules $rules)
    {
        $this->id         = $id->toString();
        $this->deck       = $deck;
        $this->cards      = new CardCollection();
        $this->smallBlind = $rules->getInitialSmallBlind()->getValue();
        $this->bigBlind   = $rules->getInitialBigBlind()->getValue();
    }

    public static function create(CardCollection $deck, Rules $rules): self
    {
        return new self(
            TableId::create(),
            $deck,
            $rules
        );
    }

    public function cards(): CardCollection
    {
        return $this->cards;
    }

    public function getRound(): int
    {
        return $this->round;
    }

    public function pickCard(int $amount): CardCollection
    {
        return $this->deck->pickCard($amount);
    }

    public function revealCards(int $amount): CardCollection
    {
        $cards = $this->deck->pickCard($amount);
        $this->cards->addCards($cards);

        return $cards;
    }

    public function currentSmallBlind(): Chip
    {
        return Chip::create($this->smallBlind);
    }

    public function currentBigBlind(): Chip
    {
        return Chip::create($this->bigBlind);
    }

    public function chips(): Chip
    {
        return new Chip($this->chips);
    }

    public function getCurrentPlayer(): PlayerId
    {
        return PlayerId::fromString($this->player);
    }

    /**
     * @param Player $nextPlayer
     *
     * @throws Exception
     */
    public function setCurrentPlayer(Player $nextPlayer)
    {
        $nextPlayer->turn();
        $this->player = $nextPlayer->getId()->toString();
    }

    public function putChips(Chip $amount): void
    {
        $this->chips += $amount->getValue();
    }

    public function deck(): CardCollection
    {
        return $this->deck;
    }
}
