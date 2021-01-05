<?php


namespace App\Tests\Unit\Game\Tournament\Domain;


use App\Game\Tournament\Domain\Player;
use App\Game\Tournament\Domain\PlayerCount;
use App\Game\Tournament\Domain\Rules;
use App\Game\Tournament\Domain\Tournament;
use App\Game\Tournament\Domain\TournamentSpecificationInterface;
use App\Game\Tournament\Domain\TournamentStatus;
use Exception;
use InvalidArgumentException;
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
    public function signUp__no_participants__joins(): void
    {
        // Given
        $participant = new Player();
        $expectedCount = 1;

        $joinSpecification = $this->createMock(TournamentSpecificationInterface::class);
        $joinSpecification->method('isSatisfiedBy')->willReturn(true);

        // When
        $t = new Tournament();
        $t->signUp($participant);

        // Then
        $this->assertSame($expectedCount, $t->participantCount());
    }

    /**
     * N
     * @test
     */
    public function signUp__has_participants__joins(): void
    {
        // Given
        $participant1       = new Player();
        $participant2       = new Player();
        $expectedCount = 2;

        // When
        $t = new Tournament();
        $t->signUp($participant1);
        $t->signUp($participant2);

        // Then
        $this->assertSame($expectedCount, $t->participantCount());
    }

    /** @test */
    public function signUp__has_minimal_required_participants__status_changes_to_ready_to_play(): void
    {
        // Given
        $participant1        = new Player();
        $participant2        = new Player();
        $expectedCount  = 2;
        $expectedStatus = TournamentStatus::READY();

        // When
        $r = new Rules(new PlayerCount($expectedCount));
        $t = new Tournament($r);
        $t->signUp($participant1);
        $t->signUp($participant2);

        // Then
        $this->assertTrue($expectedStatus->equals($t->getStatus()));
    }

    /** @test */
    public function signUp__has_max_required_participants_exceeded__throws_invalid_argument_exception(): void
    {
        // Expect
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tournament has already full amount of participants');

        // Given
        $participant1       = new Player();
        $participant2       = new Player();
        $participant3       = new Player();
        $expectedCount = 2;

        // When
        $r = new Rules(new PlayerCount(2, $expectedCount));
        $t = new Tournament($r);
        $t->signUp($participant1);
        $t->signUp($participant2);
        $t->signUp($participant3);
    }

    /** @test */
    public function signUp__on_not_pending_or_ready_tournament_throws_exception(): void
    {
        // Except
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Tournament sign up is closed');

        // Given
        $participant1       = new Player();
        $participant2       = new Player();
        $expectedCount = 2;

        // When
        $r = new Rules(new PlayerCount($expectedCount));
        $t = new Tournament($r);
        $t->signUp($participant1);
        $t->signUp($participant2);
        $t->startTournament();
        $t->signUp($participant2);
    }

    /** @test */
    public function signUp__already_joined__throws_runtime_exception(): void
    {
        // Except
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Participant already registered to this tournament');

        // Given
        $participant1 = new Player();

        // When
        $t = new Tournament();
        $t->signUp($participant1);
        $t->signUp($participant1);
    }

    /** @test */
    public function join__not_signed_up__throws_runtime_exception(): void
    {
        // Except
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Can not join this tournament because is not signed up');

        // Given
        $participant1 = new Player();

        // When
        $t = new Tournament();
        $t->join($participant1);
    }
}
