<?php


namespace App\Tests\Unit\Game\Tournament\Domain;


use App\Game\Tournament\Domain\Player;
use App\Game\Tournament\Domain\PlayerCount;
use App\Game\Tournament\Domain\Rules;
use App\Game\Tournament\Domain\Tournament;
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
    public function new__has_preparation_status(): void
    {
        // Given
        $expectedStatus = TournamentStatus::PREPARATION();

        // When
        $t      = new Tournament();
        $result = $t->getStatus();

        // Then
        $this->assertTrue($expectedStatus->equals($result));
    }

    /**
     * 1
     * @test
     */
    public function publish__tournament(): void
    {
        // When
        $t        = new Tournament();
        $expected = TournamentStatus::SIGN_UPS();
        $t->publish();

        // Then
        $this->assertTrue($expected->equals($t->getStatus()));
    }

    /** @test */
    public function publish__already_published__throws_runtime_exception(): void
    {
        // Except
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Tournament must be in preparation status to get published');

        // When
        $t = new Tournament();
        $t->publish();
        $t->publish();
    }

    /**
     * 1
     * @test
     */
    public function signUp__no_participants__joins(): void
    {
        // Given
        $participant   = new Player();
        $expectedCount = 1;

        // When
        $t = new Tournament();
        $t->publish();
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
        $participant1  = new Player();
        $participant2  = new Player();
        $expectedCount = 2;

        // When
        $t = new Tournament();
        $t->publish();
        $t->signUp($participant1);
        $t->signUp($participant2);

        // Then
        $this->assertSame($expectedCount, $t->participantCount());
    }

    /** @test */
    public function signUp__has_max_required_participants_exceeded__throws_invalid_argument_exception(): void
    {
        // Expect
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tournament has already full amount of participants');

        // Given
        $participant1  = new Player();
        $participant2  = new Player();
        $participant3  = new Player();
        $expectedCount = 2;

        // When
        $r = new Rules(new PlayerCount(2, $expectedCount));
        $t = Tournament::create($r);
        $t->publish();
        $t->signUp($participant1);
        $t->signUp($participant2);
        $t->signUp($participant3);
    }

    /** @test */
    public function signUp__tournament_not_ready_for_signups__throws_exception(): void
    {
        // Except
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Tournament sign up is closed');

        // Given
        $participant1  = new Player();
        $participant2  = new Player();
        $expectedCount = 2;

        // When
        $r = new Rules(new PlayerCount($expectedCount));
        $t = Tournament::create($r);
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
        $t->publish();
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
        $t->publish();
        $t->join($participant1);
    }

    /** @test */
    public function join__already_joined__throws_runtime_exception(): void
    {
        // Except
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Player already joined to this tournament');

        // Given
        $participant1 = new Player();
        $participant2 = new Player();

        // When
        $t = new Tournament();
        $t->publish();
        $t->signUp($participant1);
        $t->signUp($participant2);
        $t->join($participant1);
        $t->join($participant1);
    }

    /**
     * 1
     * @test
     */
    public function join__tournament(): void
    {
        // Given
        $participant1 = new Player();
        $participant2 = new Player();
        $expected     = true;

        // When
        $t = new Tournament();
        $t->publish();
        $t->signUp($participant1);
        $t->signUp($participant2);
        $t->join($participant1);
        $t->join($participant2);

        // Then
        $this->assertSame($expected, $t->hasPlayer($participant1));
    }

    /** @test */
    public function join__minimal_players_count__changes_status_to_ready_to_start(): void
    {
        // Given
        $participant1 = new Player();
        $participant2 = new Player();
        $expected     = TournamentStatus::READY();

        // When
        $t = new Tournament();
        $t->publish();
        $t->signUp($participant1);
        $t->signUp($participant2);
        $t->join($participant1);
        $t->join($participant2);

        // Then
        $this->assertTrue($expected->equals($t->getStatus()));
    }

    /**
     * 1
     * @test
     */
    public function leave__tournament(): void
    {
        // Given
        $participant1 = new Player();
        $participant2 = new Player();
        $expected     = false;

        // When
        $t = new Tournament();
        $t->publish();
        $t->signUp($participant1);
        $t->signUp($participant2);
        $t->join($participant1);
        $t->join($participant2);
        $t->leave($participant1);

        // Then
        $this->assertSame($expected, $t->hasPlayer($participant1));
    }

    /** @test */
    public function leave__not_joined__throws_invalid_argument_exception(): void
    {
        // Except
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Player is already out of this tournament');

        // Given
        $participant1 = new Player();
        $participant2 = new Player();

        // When
        $t = new Tournament();
        $t->publish();
        $t->signUp($participant1);
        $t->signUp($participant2);
        $t->join($participant1);
        $t->join($participant2);
        $t->leave($participant1);
        $t->leave($participant1);
    }


    /** @test */
    public function start__not_ready__throws_runtime_exception(): void
    {
        // Except
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Tournament is not ready to start');

        // Given
        $participant1 = new Player();
        $participant2 = new Player();

        // When
        $t = new Tournament();
        $t->publish();
        $t->signUp($participant1);
        $t->join($participant1);
        $t->start();
    }
}
