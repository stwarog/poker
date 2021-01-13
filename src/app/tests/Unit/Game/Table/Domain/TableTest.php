<?php declare(strict_types=1);


namespace Unit\Game\Table\Domain;


use App\Game\Shared\Domain\Cards\CardCollection;
use App\Game\Shared\Domain\Cards\CardDeckFactory;
use App\Game\Shared\Domain\Chip;
use App\Game\Table\Domain\Table;
use App\Game\Tournament\Domain\Player;
use App\Game\Tournament\Domain\PlayerCollection;
use App\Game\Tournament\Domain\Tournament;
use Exception;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class TableTest extends TestCase
{
    private CardCollection $deck;
    private Tournament $tournament;

    protected function setUp(): void
    {
        parent::setUp();
        $this->deck       = (new CardDeckFactory())->create();
        $this->tournament = Tournament::create();
    }

    /**
     * 1
     * @test
     */
    public function create__table(): void
    {
        // Given
        $deck               = $this->deck;
        $expectedRound      = 1;
        $expectedChips      = new Chip(0);
        $rules              = $this->tournament->getRules();
        $expectedCurrentBet = $rules->getInitialBigBlind();

        // When
        $t = Table::create($deck, $this->tournament);

        // Then
        $this->assertSame($expectedRound, $t->getRound());
        $this->assertEquals($expectedChips, $t->chips());
        $this->assertEquals($expectedCurrentBet, $t->getCurrentBet());
    }

    /** @test */
    public function pickCard__takes_from_deck_and_returns(): void
    {
        // Given
        $deck              = $this->deck;
        $expectedDeckCount = 50;

        // When
        $t = Table::create($deck, $this->tournament);
        $t->pickCard(2);

        // Then
        $this->assertSame($expectedDeckCount, $deck->count());
    }

    /** @test */
    public function revealCards__takes_from_deck_and_returns(): void
    {
        // Given
        $deck               = $this->deck;
        $expectedDeckCount  = 49;
        $expectedTakenCount = 3;

        // When
        $t     = Table::create($deck, $this->tournament);
        $taken = $t->revealCards(3);

        // Then
        $this->assertSame($expectedDeckCount, $deck->count());
        $this->assertSame($expectedTakenCount, $taken->count());
    }

    /** @test */
    public function setCurrentPlayer__makes_turn(): void
    {
        // Given
        $t = Table::create($this->deck, $this->tournament);
        $p = new Player();

        // When
        $t->setCurrentPlayer($p);

        // Then
        $this->assertTrue($p->hasTurn(), 'It is not this player turn');
    }

    /** @test */
    public function setCurrentPlayer__current_player_is_the_same__throws_exception(): void
    {
        // Except
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Attempted to set the same current player');

        // Given
        $t = Table::create($this->deck, $this->tournament);
        $p = new Player();

        // When
        $t->setCurrentPlayer($p);
        $t->setCurrentPlayer($p);
    }

    /** @test */
    public function getNextPlayer__none_has_big_blind__throws_runtime_exception(): void
    {
        // Except
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Attempted to get next player, but no Big Blind assigned');

        // Given
        $players = new PlayerCollection();
        $players->addPlayer(new Player(), new Player(), new Player());

        // When
        $t = Table::create($this->deck, $this->tournament);
        $t->getNextPlayer($players);
    }

    /** @test */
    public function getNextPlayer__two_players_on_table__returns_first_one(): void
    {
        // Given
        $t = Table::create($this->deck, $this->tournament);

        $players        = new PlayerCollection();
        $expectedPlayer = new Player();
        $bigBlindPlayer = new Player();
        $bigBlindPlayer->giveBigBlind($t);
        $players->addPlayer($expectedPlayer, $bigBlindPlayer);

        // When
        $actual = $t->getNextPlayer($players);
        $this->assertSame($expectedPlayer, $actual);
    }

    /** @test */
    public function getNextPlayer__one_player_left__nextRound(): void
    {
        // Given
        $players []     = new Player();
        $bigBlindPlayer = new Player();
        $bigBlindPlayer->giveBigBlind(Table::create($this->deck, $this->tournament));
        $players []       = $bigBlindPlayer;
        $playerCollection = $this->createMock(PlayerCollection::class);
        $playerCollection->method('getPlayersUnderGameCount')->willReturn(1);
        $playerCollection->method('toArray')->willReturn($players);
        $tournament = $this->createMock(Tournament::class);
        $tournament
            ->method('getPlayers')
            ->willReturn($playerCollection);

        $expectedRound = 2;
        $table         = Table::create($this->deck, $tournament);

        // When
        $table->nextPlayer();

        // Then
        $actualRound = $table->getRound();

        $this->assertSame($expectedRound, $actualRound);
    }
}
