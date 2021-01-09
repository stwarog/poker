<?php declare(strict_types=1);


namespace App\Game\Shared\Domain;


use App\Game\Chip;
use App\Game\Shared\Domain\Cards\CardCollection;
use App\Game\Tournament\Domain\Player;
use App\Game\Tournament\Domain\PlayerId;
use App\Game\Tournament\Domain\Tournament;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use RuntimeException;

class Table
{
    private string $id;
    private int $round = 1;
    private CardCollection $deck;
    private CardCollection $cards;
    private int $chips = 0;
    private ?string $player = null;
    private int $smallBlind;
    private int $bigBlind;
    private Tournament $tournament;
    private int $currentBet = 0;

    public function __construct(TableId $id, CardCollection $deck, Tournament $tournament)
    {
        $this->id    = $id->toString();
        $this->deck  = $deck;
        $this->cards = new CardCollection();

        $rules            = $tournament->getRules();
        $this->smallBlind = $rules->getInitialSmallBlind()->getValue();
        $this->bigBlind   = $rules->getInitialBigBlind()->getValue();
        $this->currentBet = $rules->getInitialBigBlind()->getValue();

        $this->tournament = $tournament;
    }

    public static function create(CardCollection $deck, Tournament $tournament): self
    {
        return new self(
            TableId::create(),
            $deck,
            $tournament
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

    /**
     * @throws Exception
     */
    public function nextPlayer(): void
    {
        # todo: refactor this shit
        $asCollection = new ArrayCollection($this->tournament->getPlayers());
        $current      = $this->getNextPlayer($asCollection);
        $this->setCurrentPlayer($current);
    }

    public function getCurrentBet(): Chip
    {
        return new Chip($this->currentBet);
    }

    public function getNextPlayer(ArrayCollection $collection): Player
    {
        $players     = array_values($collection->toArray());
        $hasBigBlind = array_filter($players, fn(Player $p) => $p->hasBigBlind());
        if (empty($hasBigBlind)) {
            throw new RuntimeException('Attempted to get next player, but no Big Blind assigned');
        }
        $index = array_key_first($hasBigBlind);
        $index++;

        return isset($players[$index]) ? $players[$index] : $players[0];
    }
}
