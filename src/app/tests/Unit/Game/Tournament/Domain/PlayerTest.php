<?php declare(strict_types=1);


namespace Unit\Game\Tournament\Domain;


use App\Game\Shared\Domain\Chip;
use App\Game\Shared\Domain\Cards\CardCollection;
use App\Game\Table\Domain\Table;
use App\Game\Tournament\Domain\Player;
use App\Game\Tournament\Domain\PlayerStatus;
use App\Game\Tournament\Domain\Tournament;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class PlayerTest extends TestCase
{
    private Tournament $tournament;
    private Table $table;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tournament = Tournament::create();
        $this->table      = Table::create(new CardCollection(), $this->tournament);
    }

    /** @test */
    public function player__new__has_no_chips(): void
    {
        // Given
        $expectedChipAmount = new Chip(0);

        // When
        $p = new Player();

        // Then
        $this->assertTrue($expectedChipAmount->equals($p->chips()));
        $this->assertFalse($p->hasBigBlind());
        $this->assertFalse($p->hasSmallBlind());
    }

    /**
     * 1
     * @test
     */
    public function addChips__sums_added_values(): void
    {
        // Given
        $expected = new Chip(Chip::WHITE50);

        // When
        $p = new Player();
        $p->addChips(new Chip(Chip::RED25));
        $p->addChips(new Chip(Chip::RED25));

        // Then
        $this->assertTrue($expected->equals($p->chips()));
    }

    /** @test */
    public function addChips__amount_zero__throws_invalid_argument_exception(): void
    {
        // Except
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Can not add 0 value chips');

        // Given
        $chip = new Chip(0);

        // When
        $p = new Player();
        $p->addChips($chip);
    }

    /** @test */
    public function takeChips__player_has_some_chips__reduces_amount_by_given_value(): void
    {
        // Given
        $expected = new Chip(Chip::RED25);
        $take     = new Chip(Chip::RED25);

        // When
        $p = new Player();
        $p->addChips(new Chip(Chip::WHITE50));
        $p->takeChips($take);

        // Then
        $this->assertTrue($expected->equals($p->chips()));
    }

    /** @test */
    public function takeChips__amount_is_zero__throws_invalid_argument_exception(): void
    {
        // Except
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Can not take 0 value chip');

        // Given
        $take = new Chip(0);

        // When
        $p = new Player();
        $p->takeChips($take);
    }

    /** @test */
    public function takeChips__amount_after_reduce_is_less_than_zero__throws_invalid_argument_exception(): void
    {
        // Except
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Requested to take 50 chips but user has only 25');

        // Given
        $take = new Chip(Chip::WHITE50);

        // When
        $p = new Player();
        $p->addChips(new Chip(Chip::RED25));
        $p->takeChips($take);
    }

    /** @test */
    public function getStatus__new_player__has_active(): void
    {
        // Given
        $expectedStatus = PlayerStatus::ACTIVE();

        // When
        $p = new Player();

        // Then
        $this->assertTrue($expectedStatus->equals($p->getStatus()));
    }

    /** @test */
    public function getStatus__player_has_no_chips__has_lost_status(): void
    {
        // Given
        $expectedStatus = PlayerStatus::LOST();

        // When
        $p = new Player();
        $p->addChips(new Chip(Chip::RED25));
        $this->assertTrue(PlayerStatus::ACTIVE()->equals($p->getStatus()));
        $p->takeChips(new Chip(Chip::RED25));

        // Then
        $this->assertTrue($expectedStatus->equals($p->getStatus()));
    }

    /** @test */
    public function giveSmallBlind(): void
    {
        // Given
        $p = new Player();
        $t = Table::create(new CardCollection(), $this->tournament);

        // When
        $p->giveSmallBlind($t);

        // Then
        $this->assertTrue($p->hasSmallBlind());
    }

    /** @test */
    public function giveSmallBlind_has_not_none_role_throws_runtime_exception(): void
    {
        // Except
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Player can not have any role to give small blind');

        // Given
        $p = new Player();
        $t = Table::create(new CardCollection(), $this->tournament);

        // When
        $p->giveSmallBlind($t);
        $p->giveSmallBlind($t);
    }

    /** @test */
    public function giveBigBlind(): void
    {
        // Given
        $p = new Player();
        $t = Table::create(new CardCollection(), $this->tournament);

        // When
        $p->giveBigBlind($t);

        // Then
        $this->assertTrue($p->hasBigBlind());
    }

    /** @test */
    public function giveBigBlind_has_not_none_role_throws_runtime_exception(): void
    {
        // Except
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Player can not have any role to give big blind');

        // Given
        $p = new Player();
        $t = Table::create(new CardCollection(), $this->tournament);

        // When
        $p->giveBigBlind($t);
        $p->giveBigBlind($t);
    }

    /** @test */
    public function turn(): void
    {
        // Given
        $p              = new Player();
        $expectedStatus = true;

        // When
        $p->turn();

        // Then
        $this->assertSame($expectedStatus, $p->hasTurn());
    }

    /** @test */
    public function turn__has_already_turn__throws_exception(): void
    {
        // Except
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Already has turn');

        // Given
        $p = new Player();

        // When
        $p->turn();
        $p->turn();
    }

    /**
     * 1
     * @test
     */
    public function fold__has_turn__changes_player(): void
    {
        // Given
        $p              = new Player();
        $expectedStatus = false;

        $table = $this->createMock(Table::class);
        $table
            ->expects($this->once())
            ->method('nextPlayer');

        // When
        $p->turn();
        $p->fold($table);

        // Then
        $this->assertSame($expectedStatus, $p->hasTurn());
    }

    /** @test */
    public function fold__player_has_no_turn__throws_exception(): void
    {
        // Except
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not this player turn');

        // Given
        $p = new Player();

        // When
        $p->fold($this->table);
    }

    /** @test */
    public function call__has_turn__changes_player(): void
    {
        // Given
        $p = new Player();
        $p->addChips(new Chip(20));
        $expectedStatus           = false;
        $expectedPlayerCurrentBet = new Chip(20);

        $table = $this->createMock(Table::class);

        $table
            ->expects($this->once())
            ->method('getCurrentBet')
            ->willReturn(new Chip(20));

        $table
            ->expects($this->once())
            ->method('nextPlayer');

        // When
        $p->turn();
        $p->call($table);

        // Then
        $this->assertSame($expectedStatus, $p->hasTurn());
        $this->assertEquals($expectedPlayerCurrentBet, $p->getCurrentBet());
    }

    /** @test */
    public function call__has_turn__changes_player_transfers_current_bet_from_player_to_table(): void
    {
        // Given
        $p = new Player();
        $p->addChips(new Chip(50));

        $expectedPlayerChips = new Chip(30);
        $expectedStatus      = false;
        $currentBet          = new Chip(20);

        $table = $this->createMock(Table::class);

        $table
            ->expects($this->once())
            ->method('getCurrentBet')
            ->willReturn($currentBet);

        $table
            ->expects($this->once())
            ->method('putChips')
            ->with($currentBet);

        // When
        $p->turn();
        $p->call($table);

        // Then
        $this->assertSame($expectedStatus, $p->hasTurn());
        $this->assertEquals($expectedPlayerChips, $p->chips());
    }

    /** @test */
    public function call__player_has_no_turn__throws_exception(): void
    {
        // Except
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not this player turn');

        // Given
        $p = new Player();

        // When
        $p->call($this->table);
    }

    /** @test */
    public function raise__has_turn__changes_player(): void
    {
        // Given
        $p = new Player();
        $p->addChips(new Chip(200));
        $expectedStatus           = false;
        $raiseChips               = new Chip(40);
        $expectedPlayerCurrentBet = new Chip(40);

        $table = $this->createMock(Table::class);

        $table
            ->expects($this->once())
            ->method('nextPlayer');

        // When
        $p->turn();
        $p->raise($table, $raiseChips);

        // Then
        $this->assertSame($expectedStatus, $p->hasTurn());
        $this->assertEquals($expectedPlayerCurrentBet, $p->getCurrentBet());
    }

    /** @test */
    public function raise__player_has_no_turn__throws_exception(): void
    {
        // Except
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not this player turn');

        // Given
        $p = new Player();

        // When
        $p->raise($this->table, new Chip(20));
    }

    /** @test */
    public function raise__has_turn__changes_player_transfers_raised_bet_from_player_to_table(): void
    {
        // Given
        $p = new Player();
        $p->addChips(new Chip(1000));

        $expectedPlayerChips = new Chip(900);
        $expectedStatus      = false;
        $raiseChips          = new Chip(100);

        $table = $this->createMock(Table::class);

        $table
            ->expects($this->once())
            ->method('putChips')
            ->with($raiseChips);

        // When
        $p->turn();
        $p->raise($table, $raiseChips);

        // Then
        $this->assertSame($expectedStatus, $p->hasTurn());
        $this->assertEquals($expectedPlayerChips, $p->chips());
    }

    /** @test */
    public function raise__less_than_double_big_blind__throws_invalid_argument_exception(): void
    {
        // Except
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bet must be at least twice big blind value');

        // Given
        $p = new Player();
        $p->addChips(new Chip(1000));

        $raiseChips = new Chip(100);

        $table = $this->createMock(Table::class);

        $table
            ->expects($this->once())
            ->method('currentBigBlind')
            ->willReturn(new Chip(100));

        // When
        $p->turn();
        $p->raise($table, $raiseChips);
    }

    /** @test */
    public function allIn__calls_raise_with_all_users_chips(): void
    {
        // Given
        $p = new Player();
        $p->addChips(new Chip(1000));

        $expectedPlayerChips = new Chip(0);
        $expectedStatus      = false;
        $raiseChips          = new Chip(1000);

        $table = $this->createMock(Table::class);

        $table
            ->expects($this->once())
            ->method('putChips')
            ->with($raiseChips);

        // When
        $p->turn();
        $p->allIn($table);

        // Then
        $this->assertSame($expectedStatus, $p->hasTurn());
        $this->assertEquals($expectedPlayerChips, $p->chips());
    }
}
