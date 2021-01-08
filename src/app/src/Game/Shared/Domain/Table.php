<?php declare(strict_types=1);


namespace App\Game\Shared\Domain;


use App\Game\Chip;
use App\Game\Shared\Domain\Cards\CardCollection;
use App\Game\Tournament\Domain\Player;
use App\Game\Tournament\Domain\PlayerId;
use App\Game\Tournament\Domain\Rules;
use Exception;
use RuntimeException;

class Table
{
    private TableId $id;
    private int $round = 1;
    private CardCollection $deck;
    private CardCollection $cardOnTable;
    private int $chipsOnTable = 0;
    private ?string $currentPlayer = null;
    private int $currentSmallBlind = 0;
    private int $currentBigBlind = 0;

    public function __construct(TableId $id, CardCollection $deck, Rules $rules)
    {
        $this->id                = $id;
        $this->deck              = $deck;
        $this->cardOnTable       = new CardCollection();
        $this->currentSmallBlind = $rules->getInitialSmallBlind()->getValue();
        $this->currentBigBlind   = $rules->getInitialBigBlind()->getValue();
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
        return $this->cardOnTable;
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
        $this->cardOnTable->addCards($cards);

        return $cards;
    }

    /**
     * @param Player $nextPlayer
     *
     * @throws Exception
     */
    public function setCurrentPlayer(Player $nextPlayer)
    {
        $nextPlayer->turn();
        $this->currentPlayer = $nextPlayer->getId()->toString();
    }

    public function currentSmallBlind(): Chip
    {
        return Chip::create($this->currentSmallBlind);
    }

    public function currentBigBlind(): Chip
    {
        return Chip::create($this->currentBigBlind);
    }

    public function getChips(): Chip
    {
        return new Chip($this->chipsOnTable);
    }

    public function getCurrentPlayer(): PlayerId
    {
        return PlayerId::fromString($this->currentPlayer);
    }

    public function putChips(Chip $amount): void
    {
        $this->chipsOnTable += $amount->getValue();
    }

    public function deck(): CardCollection
    {
        return $this->deck;
    }

    public function initialize(Rules $rules): void
    {
        if ($this->round !== 1) {
            throw new RuntimeException('Can not initialize table when game is in progress');
        }
        $this->currentSmallBlind = $rules->getInitialSmallBlind()->getValue();
        $this->currentBigBlind   = $rules->getInitialBigBlind()->getValue();
    }
}
