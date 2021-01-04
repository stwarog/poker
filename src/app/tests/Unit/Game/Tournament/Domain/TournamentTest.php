<?php


namespace App\Tests\Unit\Game\Tournament\Domain;


use App\Game\Tournament\Domain\Player;
use App\Game\Tournament\Domain\PlayerCount;
use App\Game\Tournament\Domain\Rules;
use App\Game\Tournament\Domain\Tournament;
use App\Game\Tournament\Domain\TournamentSpecificationInterface;
use App\Game\Tournament\Domain\TournamentStatus;
use Exception;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class TournamentTest extends TestCase
{
    /**
     * 0
     * @test
     */
    public function new__has_pending_status(): void
    {
        // Given
        $expectedStatus = TournamentStatus::PENDING();
        
        // When
        $t = new Tournament();
        $result = $t->getStatus();
        
        // Then
        $this->assertTrue($expectedStatus->equals($result));
    }

    /**
     * 1
     * @test
     */
    public function signUp__no_players__joins(): void
    {
        // Given
        $player = new Player();
        $expectedCount = 1;

        $joinSpecification = $this->createMock(TournamentSpecificationInterface::class);
        $joinSpecification->method('isSatisfiedBy')->willReturn(true);

        // When
        $t = new Tournament();
        $t->signUp($player);

        // Then
        $this->assertSame($expectedCount, $t->playersCount());
    }

    /**
     * N
     * @test
     */
    public function signUp__has_players__joins(): void
    {
        // Given
        $player1       = new Player();
        $player2       = new Player();
        $expectedCount = 2;

        // When
        $t = new Tournament();
        $t->signUp($player1);
        $t->signUp($player2);

        // Then
        $this->assertSame($expectedCount, $t->playersCount());
    }

    /** @test */
    public function signUp__has_minimal_required_players__status_changes_to_ready_to_play(): void
    {
        // Given
        $player1        = new Player();
        $player2        = new Player();
        $expectedCount  = 2;
        $expectedStatus = TournamentStatus::READY();

        // When
        $r = new Rules(new PlayerCount($expectedCount));
        $t = new Tournament($r);
        $t->signUp($player1);
        $t->signUp($player2);

        // Then
        $this->assertTrue($expectedStatus->equals($t->getStatus()));
    }

    /** @test */
    public function signUp__on_ready_tournament_throws_exception(): void
    {
        // Except
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Tournament sign up is closed');

        // Given
        $player1       = new Player();
        $player2       = new Player();
        $player3       = new Player();
        $expectedCount = 2;

        // When
        $r = new Rules(new PlayerCount($expectedCount));
        $t = new Tournament($r);
        $t->signUp($player1);
        $t->signUp($player2);
        $t->signUp($player3);
    }

    /** @test */
    public function signUp__already_joined__throws_runtime_exception(): void
    {
        // Except
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Player already registered to this tournament');

        // Given
        $player1 = new Player();

        // When
        $t = new Tournament();
        $t->signUp($player1);
        $t->signUp($player1);
    }
}
