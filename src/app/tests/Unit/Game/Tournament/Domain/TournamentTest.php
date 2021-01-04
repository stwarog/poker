<?php


namespace App\Tests\Unit\Game\Tournament\Domain;


use App\Game\Tournament\Domain\Player;
use App\Game\Tournament\Domain\Tournament;
use App\Game\Tournament\Domain\TournamentSpecificationInterface;
use App\Game\Tournament\Domain\TournamentStatus;
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
        $t->signUp($player, $joinSpecification);

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
        $player1 = new Player();
        $player2 = new Player();
        $expectedCount = 2;

        $joinSpecification = $this->createMock(TournamentSpecificationInterface::class);
        $joinSpecification->method('isSatisfiedBy')->willReturn(true);

        // When
        $t = new Tournament();
        $t->signUp($player1, $joinSpecification);
        $t->signUp($player2, $joinSpecification);

        // Then
        $this->assertSame($expectedCount, $t->playersCount());
    }

    /** @test */
    public function signUp__not_ready_status__throws_runtime_exception(): void
    {
        // Except
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Tournament is not playable yet');

        // Given
        $player1 = new Player();
        $joinSpecification = $this->createMock(TournamentSpecificationInterface::class);
        $joinSpecification->method('isSatisfiedBy')->willReturn(false);

        // When
        $t = new Tournament();
        $t->signUp($player1, $joinSpecification);
    }

    /** @test */
    public function signUp__already_joined__throws_runtime_exception(): void
    {
        // Except
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Player already registered to this tournament');

        // Given
        $player1 = new Player();
        $joinSpecification = $this->createMock(TournamentSpecificationInterface::class);
        $joinSpecification->method('isSatisfiedBy')->willReturn(true);

        // When
        $t = new Tournament();
        $t->signUp($player1, $joinSpecification);
        $t->signUp($player1, $joinSpecification);
    }
}
