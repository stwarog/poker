<?php declare(strict_types=1);


namespace App\Game\Table\Domain;


use App\Game\Shared\Domain\Cards\CardCollection;
use App\Game\Shared\Domain\Chip;
use App\Game\Shared\Domain\TableId;
use App\Game\Tournament\Domain\Tournament;
use App\Game\Tournament\Domain\TournamentId;
use App\Game\Tournament\Domain\TournamentStatus;
use Exception;
use InvalidArgumentException;
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

    public function __construct(TableId $id, CardCollection $deck, TournamentId $tournament)
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

    public function getId(): TableId
    {
        return TableId::fromString($this->id);
    }

    public static function create(CardCollection $deck, TournamentId $tournament): self
    {
        return new self(
            TableId::create(),
            $deck,
            $tournament
        );
    }
//
//    public function cards(): CardCollection
//    {
//        return $this->cards;
//    }
//
//    public function getRound(): int
//    {
//        return $this->round;
//    }
//
//    public function pickCard(int $amount): CardCollection
//    {
//        return $this->deck->pickCard($amount);
//    }
//
//    public function revealCards(int $amount): CardCollection
//    {
//        $cards = $this->deck->pickCard($amount);
//        $this->cards->addCards($cards);
//
//        return $cards;
//    }
//
//    public function currentSmallBlind(): Chip
//    {
//        return Chip::create($this->smallBlind);
//    }
//
//    public function currentBigBlind(): Chip
//    {
//        return Chip::create($this->bigBlind);
//    }
//
//    public function chips(): Chip
//    {
//        return new Chip($this->chips);
//    }
//
//    /**
//     * @throws Exception
//     */
//    public function nextPlayer(): void
//    {
//        $next = $this->getNextPlayer($this->tournament->getPlayers());
//
//        if ($this->tournament->getPlayers()->getPlayersUnderGameCount() === 1) {
//            $this->nextRound();
//
//            return;
//        }
//
//        $this->setCurrentPlayer($next);
//    }
//
//    public function getNextPlayer(PlayerCollection $players): ?Player
//    {
//        $hasBigBlind = array_filter($players->toArray(), fn(Player $p) => $p->hasBigBlind());
//        if (empty($hasBigBlind)) {
//            throw new RuntimeException('Attempted to get next player, but no Big Blind assigned');
//        }
//        $index = array_key_first($hasBigBlind);
//        $index++;
//
//        return isset($players[$index]) ? $players[$index] : $players[0];
//    }
//
//    private function nextRound()
//    {
//        $this->round++;
//    }
//
//    /**
//     * @param Player $nextPlayer
//     *
//     * @throws Exception
//     */
//    public function setCurrentPlayer(Player $nextPlayer)
//    {
//        if ($hasCurrentPlayer = !empty($this->getCurrentPlayer()) && $this->getCurrentPlayer()->equals($nextPlayer->getId())) {
//            throw new Exception('Attempted to set the same current player');
//        }
//        $nextPlayer->turn();
//        $this->player = $nextPlayer->getId()->toString();
//    }
//
//    public function getCurrentPlayer(): ?PlayerId
//    {
//        if (empty($this->player)) {
//            return null;
//        }
//
//        return PlayerId::fromString($this->player);
//    }
//
//    public function putChips(Chip $amount): void
//    {
//        $this->chips += $amount->getValue();
//    }
//
//    public function deck(): CardCollection
//    {
//        return $this->deck;
//    }
//
//    public function getCurrentBet(): Chip
//    {
//        return new Chip($this->currentBet);
//    }
//
//    public function getId(): TableId
//    {
//        return TableId::fromString($this->id);
//    }
//
//    /**
//     * @param PlayerId $player
//     *
//     * @throws RuntimeException
//     */
//    public function join(PlayerId $player): void
//    {
//        if (false === $this->hasParticipant($player)) {
//            throw new RuntimeException('Can not join this tournament because is not signed up');
//        }
//
//        if (false === $this->hasPlayer($player)) {
//            # throw new RuntimeException('Player already joined to this tournament');
//            $p = $this->getParticipate($player);
//            $this->players->set($p->getId()->toString(), $p);
//        }
//
//        if ($isReadyToStart = $this->getPlayersCount() >= $this->getRules()->getPlayerCount()->getMin()) {
//            $this->status = TournamentStatus::READY;
//        }
//    }
//
//    public function hasPlayer(PlayerId $player): bool
//    {
//        return false === $this->players->filter(fn(Player $p) => $p->getId()->equals($player))->isEmpty();
//    }
//
//
//    /**
//     * @param Table $table
//     *
//     * @throws Exception
//     */
//    private function flop(Table $table): void
//    {
//        $this->verifyIsStarted();
//
//        $players = $this->getPlayers();
//
//        $players[0]->giveSmallBlind($table);
//        $players[1]->giveBigBlind($table);
//
//        $table->nextPlayer();
//
//        foreach ($players as $player) {
//            $player->pickCards($table, 2);
//        }
//
//        $table->revealCards(3);
//    }
//
//    private function verifyIsStarted(): void
//    {
//        if ($this->status !== TournamentStatus::STARTED) {
//            throw new RuntimeException('Tournament must be started to perform this action');
//        }
//    }
//
//
//    public function leave(PlayerId $player): void
//    {
//        if (false === $this->hasPlayer($player)) {
//            throw new InvalidArgumentException('Player is already out of this tournament');
//        }
//
//        $this->players->remove($player->toString());
//    }
//
//
//    public function getPlayerChips(PlayerId $p): Chip
//    {
//        return $this->players->get($p->toString())->chips();
//    }
//
//
//    public function getCurrentPlayer(): PlayerId
//    {
//        return $this->table->getCurrentPlayer();
//    }
//
//
//    /**
//     * @param PlayerId $id
//     *
//     * @throws Exception
//     */
//    public function fold(PlayerId $id): void
//    {
//        $this->verifyIsStarted();
//        $p = $this->getPlayer($id);
//        $p->fold($this->table);
//    }
//
//    private function getPlayer(PlayerId $playerId): Player
//    {
//        return $this->players->get($playerId->toString());
//    }
//
//
//    /**
//     * @param PlayerId $id
//     *
//     * @throws Exception
//     */
//    public function call(PlayerId $id): void
//    {
//        $this->verifyIsStarted();
//        $p = $this->getPlayer($id);
//        $p->call($this->table);
//    }
//
//    /**
//     * @param PlayerId $id
//     * @param Chip     $amount
//     *
//     * @throws Exception
//     */
//    public function raise(PlayerId $id, Chip $amount): void
//    {
//        $this->verifyIsStarted();
//        $p = $this->getPlayer($id);
//        $p->raise($this->table, $amount);
//    }
//
//    /**
//     * @param PlayerId $id
//     *
//     * @throws Exception
//     */
//    public function allIn(PlayerId $id): void
//    {
//        $this->verifyIsStarted();
//        $p = $this->getPlayer($id);
//        $p->allIn($this->table);
//    }
}
