<?php declare(strict_types=1);


namespace Unit\Game\Table\Domain;


use App\Game\Shared\Domain\Cards\CardCollection;
use App\Game\Shared\Domain\Cards\CardDeckFactory;
use App\Game\Shared\Domain\Chip;
use App\Game\Table\Domain\Player;
use App\Game\Table\Domain\PlayerCollection;
use App\Game\Table\Domain\PlayerId;
use App\Game\Table\Domain\PlayerStatus;
use App\Game\Table\Domain\Table;
use App\Game\Tournament\Domain\PlayerCount;
use App\Game\Tournament\Domain\Rules;
use App\Game\Tournament\Domain\Tournament;
use App\Game\Tournament\Domain\TournamentStatus;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class TableTest extends TestCase
{
    private CardCollection $deck;
    private Tournament $tournament;

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

    protected function setUp(): void
    {
        parent::setUp();
        $this->deck       = (new CardDeckFactory())->create();
        $this->tournament = Tournament::create();
    }

    # trash

//
//    /** @test */
//    public function join__not_signed_up__throws_runtime_exception(): void
//    {
//        // Except
//        $this->expectException(RuntimeException::class);
//        $this->expectExceptionMessage('Can not join this tournament because is not signed up');
//
//        // When
//        $t = Tournament::create();
//        $t->publish();
//        $t->join(PlayerId::create());
//    }
//
//    /** @test */
//    public function join__already_joined__throws_runtime_exception(): void
//    {
//        $this->markTestSkipped('Should allow re-join if disconnected');
//
//        // Except
//        $this->expectException(RuntimeException::class);
//        $this->expectExceptionMessage('Player already joined to this tournament');
//
//        // When
//        $t = new Tournament();
//        $t->publish();
//        $p1 = $t->signUp();
//        $t->signUp();
//        $t->join($p1);
//        $t->join($p1);
//    }
//
//    /**
//     * 1
//     * @test
//     */
//    public function join__player_receives_rule_initial_chip_amount(): void
//    {
//        // Given
//        $r = Rules::createDefaults();
//
//        $expected             = true;
//        $expectedPlayerCounts = 2;
//        $expectedChipAmount   = $r->getInitialChipsPerPlayer();
//
//        // When
//        $t = Tournament::create($r);
//        $t->publish();
//        $p1 = $t->signUp();
//        $p2 = $t->signUp();
//        $t->join($p1);
//        $t->join($p2);
//
//        // Then
//        $this->assertSame($expected, $t->hasPlayer($p1));
//        $this->assertSame($expected, $t->hasPlayer($p2));
//        $this->assertSame($expectedPlayerCounts, $t->getPlayersCount());
//        $this->assertTrue($expectedChipAmount->equals($t->getPlayerChips($p1)));
//        $this->assertTrue($expectedChipAmount->equals($t->getPlayerChips($p2)));
//    }
//
//    /** @test */
//    public function join__minimal_players_count__changes_status_to_ready_to_start(): void
//    {
//        // Given
//        $expected = TournamentStatus::READY();
//
//        // When
//        $t = new Tournament();
//        $t->publish();
//        $p1 = $t->signUp();
//        $p2 = $t->signUp();
//        $t->join($p1);
//        $t->join($p2);
//
//        // Then
//        $this->assertTrue($expected->equals($t->getStatus()));
//    }
//
//    /**
//     * 1
//     * @test
//     */
//    public function leave__tournament(): void
//    {
//        // Given
//        $expected = false;
//
//        // When
//        $t = new Tournament();
//        $t->publish();
//        $p1 = $t->signUp();
//        $p2 = $t->signUp();
//        $t->join($p1);
//        $t->join($p2);
//        $t->leave($p1);
//
//        // Then
//        $this->assertSame($expected, $t->hasPlayer($p1));
//    }
//
//    /** @test */
//    public function leave__not_joined__throws_invalid_argument_exception(): void
//    {
//        // Except
//        $this->expectException(InvalidArgumentException::class);
//        $this->expectExceptionMessage('Player is already out of this tournament');
//
//        // When
//        $t = new Tournament();
//        $t->publish();
//        $p1 = $t->signUp();
//        $p2 = $t->signUp();
//        $t->join($p1);
//        $t->join($p2);
//        $t->leave($p1);
//        $t->leave($p1);
//    }
//
//    /** @test */
//    public function start__not_ready__throws_runtime_exception(): void
//    {
//        // Except
//        $this->expectException(RuntimeException::class);
//        $this->expectExceptionMessage('Tournament is not ready to start');
//
//        // When
//        $t = new Tournament();
//        $t->publish();
//        $p = $t->signUp();
//        $t->join($p);
//        $t->start($this->table);
//    }
//
//    /**
//     * 1
//     * @test
//     */
//    public function start__blinds_and_two_cards_assigned_each_player(): void
//    {
//        // Given
//        $expectedDeckCardCountAfterStart = 52 - 4 - 3;
//
//        $initialChips      = new Chip(100);
//        $initialSmallBlind = new Chip(5);
//        $initialBigBlind   = new Chip(10);
//
//        $rules = new Rules(
//            new PlayerCount(2, 5),
//            $initialChips,
//            $initialSmallBlind,
//            $initialBigBlind,
//        );
//
//        $expectedBigPlayerChips   = new Chip(90);
//        $expectedSmallPlayerChips = new Chip(95);
//
//        $t = Tournament::create($rules);
//        $t->publish();
//
//        $p1 = $t->signUp();
//        $p2 = $t->signUp();
//        $t->join($p1);
//        $t->join($p2);
//
//        $table = Table::create($this->deck, $t);
//
//        $expectedCardsCount = 2;
//
//        // When
//        $t->start($table);
//
//        // Then
//
//        $anySmallBlind = false;
//        $anyBigBlind   = false;
//
//        foreach ($t->getPlayers() as $player) {
//            $this->assertSame($expectedCardsCount, $player->getCards()->count());
//
//            if ($player->hasSmallBlind()) {
//                $this->assertTrue(
//                    $expectedSmallPlayerChips->equals($player->chips()),
//                    sprintf(
//                        'Small blind should have #%s but have #%s',
//                        $expectedSmallPlayerChips->getValue(),
//                        $player->chips()->getValue()
//                    )
//                );
//                $anySmallBlind = true;
//            }
//
//            if ($player->hasBigBlind()) {
//                $this->assertTrue(
//                    $expectedBigPlayerChips->equals($player->chips()),
//                    sprintf(
//                        'Big blind should have #%s but have #%s',
//                        $expectedBigPlayerChips->getValue(),
//                        $player->chips()->getValue()
//                    )
//                );
//                $anyBigBlind = true;
//            }
//        }
//
//        $this->assertSame($expectedDeckCardCountAfterStart, $table->deck()->count());
//        $this->assertTrue($anySmallBlind);
//        $this->assertTrue($anyBigBlind);
//    }
//
//    /** @test */
//    public function start__when_two_players(): void
//    {
//        // Given
//        $t = Tournament::create();
//        $t->publish();
//
//        $p1 = $t->signUp();
//        $p2 = $t->signUp();
//        $t->join($p1);
//        $t->join($p2);
//
//        $table = Table::create($this->deck, $t);
//
//        // When
//        $t->start($table);
//
//        // Then
//        $players = $t->getPlayers();
//
//        $this->assertTrue($players[0]->hasTurn());
//    }
//
//    /** @test */
//    public function start__when_three_players__third_turn(): void
//    {
//        // Given
//        $t = Tournament::create();
//        $t->publish();
//
//        $p1 = $t->signUp();
//        $p2 = $t->signUp();
//        $p3 = $t->signUp();
//        $t->join($p1);
//        $t->join($p2);
//        $t->join($p3);
//
//        $table = Table::create($this->deck, $t);
//
//        // When
//        $t->start($table);
//
//        // Then
//        $players       = $t->getPlayers();
//        $thirdPlayer   = $players[2];
//        $thirdPlayerId = $thirdPlayer->getId();
//
//        $this->assertTrue($thirdPlayerId->equals($t->getCurrentPlayer()));
//        $this->assertTrue($thirdPlayer->hasTurn());
//    }
//
//    /** @test */
//    public function start__tournament_receives_flop(): void
//    {
//        // Given
//        $expectedFlopCount          = 3;
//        $expectedDeckCountAfterFlop = 52 - (3 * 2) - $expectedFlopCount;
//
//        $t = Tournament::create();
//        $t->publish();
//
//        $p1 = $t->signUp();
//        $p2 = $t->signUp();
//        $p3 = $t->signUp();
//        $t->join($p1);
//        $t->join($p2);
//        $t->join($p3);
//
//        $table = Table::create($this->deck, $t);
//
//        // When
//        $t->start($table);
//
//        // Then
//        $this->assertSame($expectedDeckCountAfterFlop, $table->deck()->count());
//        $this->assertEquals($expectedFlopCount, $table->cards()->count());
//    }
//
//    /** @test */
//    public function start__has_not_joined_players__marks_them_as_not_joined(): void
//    {
//        // Given
//        $expectedNotJoinedPlayers = 1;
//
//        $t = Tournament::create();
//        $t->publish();
//
//        $p1 = $t->signUp();
//        $p2 = $t->signUp();
//        $t->signUp();
//        $t->join($p1);
//        $t->join($p2);
//
//        $table = Table::create($this->deck, $t);
//
//        // When
//        $t->start($table);
//
//        // Then
//        $actualNotJoined = 0;
//        /** @var Player $p */
//        foreach ($t->getParticipants() as $p) {
//            if ($p->getStatus()->equals(PlayerStatus::NOT_JOINED())) {
//                $actualNotJoined++;
//            }
//        }
//        $this->assertSame($expectedNotJoinedPlayers, $actualNotJoined);
//    }
//
//    /** @test */
//    public function start__tournament_receives_blinds_values(): void
//    {
//        // Given
//        $initialChips      = new Chip(100);
//        $initialSmallBlind = new Chip(5);
//        $initialBigBlind   = new Chip(10);
//
//        $rules = new Rules(
//            new PlayerCount(2, 5),
//            $initialChips,
//            $initialSmallBlind,
//            $initialBigBlind,
//        );
//
//        $expectedTableChips = new Chip(15);
//
//        $t = Tournament::create($rules);
//        $t->publish();
//        $p1 = $t->signUp();
//        $p2 = $t->signUp();
//        $t->join($p1);
//        $t->join($p2);
//
//        $table = Table::create($this->deck, $t);
//
//        // When
//        $t->start($table);
//
//        // Then
//        $this->assertTrue(
//            $expectedTableChips->equals($table->chips()),
//            sprintf(
//                'Expected to have #%s chips on the table, but have #%s',
//                (string) $expectedTableChips,
//                (string) $table->chips()
//            )
//        );
//    }
//
//    /** @test */
//    public function start__each_player_receives_initial_chips(): void
//    {
//        // Given
//        $initialChips      = new Chip(100);
//        $initialSmallBlind = new Chip(5);
//        $initialBigBlind   = new Chip(10);
//
//        $rules = new Rules(
//            new PlayerCount(2, 5),
//            $initialChips,
//            $initialSmallBlind,
//            $initialBigBlind,
//        );
//
//        $t = Tournament::create($rules);
//        $t->publish();
//        $p1 = $t->signUp();
//        $p2 = $t->signUp();
//        $t->join($p1);
//        $t->join($p2);
//
//        $table = Table::create($this->deck, $t);
//
//        // When
//        $t->start($table);
//
//        // Then
//        foreach ($t->getPlayers() as $player) {
//            $expected = $initialChips->getValue();
//            $actual   = $player->chips()->getValue();
//
//            if ($player->hasBigBlind()) {
//                $expected -= $initialBigBlind->getValue();
//            }
//            if ($player->hasSmallBlind()) {
//                $expected -= $initialSmallBlind->getValue();
//            }
//
//            $this->assertEquals(
//                $expected,
//                $actual,
//                sprintf('Players has not expected initial #%d chips, but #%s', $expected, $actual)
//            );
//        }
//    }
//
//    /** @test */
//    public function fold__tournament_not_started__throws_runtime_exception(): void
//    {
//        // Except
//        $this->expectException(RuntimeException::class);
//        $this->expectExceptionMessage('Tournament must be started to perform this action');
//
//        // Given
//        $player = PlayerId::create();
//
//        // When
//        $t = Tournament::create();
//        $t->fold($player);
//    }
//
//    /** @test */
//    public function call__tournament_not_started__throws_runtime_exception(): void
//    {
//        // Except
//        $this->expectException(RuntimeException::class);
//        $this->expectExceptionMessage('Tournament must be started to perform this action');
//
//        // Given
//        $player = PlayerId::create();
//
//        // When
//        $t = Tournament::create();
//        $t->call($player);
//    }
//
//    /** @test */
//    public function raise__tournament_not_started__throws_runtime_exception(): void
//    {
//        // Except
//        $this->expectException(RuntimeException::class);
//        $this->expectExceptionMessage('Tournament must be started to perform this action');
//
//        // Given
//        $player = PlayerId::create();
//
//        // When
//        $t = Tournament::create();
//        $t->raise($player, new Chip(20));
//    }
//
//    /** @test */
//    public function allIn__tournament_not_started__throws_runtime_exception(): void
//    {
//        // Except
//        $this->expectException(RuntimeException::class);
//        $this->expectExceptionMessage('Tournament must be started to perform this action');
//
//        // Given
//        $player = PlayerId::create();
//
//        // When
//        $t = Tournament::create();
//        $t->allIn($player);
//    }
}
