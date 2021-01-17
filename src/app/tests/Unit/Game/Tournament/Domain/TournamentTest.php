<?php


namespace Unit\Game\Tournament\Domain;


use App\Account\Domain\AccountId;
use App\Game\Shared\Domain\Chip;
use App\Game\Shared\Domain\TableId;
use App\Game\Tournament\Domain\ParticipantId;
use App\Game\Tournament\Domain\PlayerCount;
use App\Game\Tournament\Domain\Rules;
use App\Game\Tournament\Domain\Tournament;
use App\Game\Tournament\Domain\TournamentStatus;
use App\Game\Tournament\Event\ParticipantSignedIn;
use App\Game\Tournament\Event\TournamentCreated;
use App\Game\Tournament\Event\TournamentPublished;
use App\Game\Tournament\Event\TournamentStarted;
use App\Shared\Domain\AbstractDomainEvent;
use App\Shared\Domain\Minutes;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class TournamentTest extends TestCase
{
    /** @test */
    public function new__has_preparation_status(): void
    {
        // Given
        $expectedStatus = TournamentStatus::PREPARATION();

        // When
        $t      = Tournament::create();
        $result = $t->getStatus();

        // Then
        $this->assertTrue($expectedStatus->equals($result));
        $this->assertNotEmpty($t->getId());
    }

    /** @test */
    public function new__tournament_created_event_recorded(): void
    {
        // When
        $t      = Tournament::create();

        // Then
        $actual = array_filter($t->pullDomainEvents(), fn(AbstractDomainEvent $e) => $e::eventName() === TournamentCreated::eventName());
        $this->assertNotEmpty($actual);
    }

    /** @test */
    public function publish__tournament__status_changes_to_sign_ups(): void
    {
        // When
        $t        = Tournament::create();
        $expected = TournamentStatus::SIGN_UPS();
        $t->publish();

        // Then
        $this->assertTrue($expected->equals($t->getStatus()));
    }

    /** @test */
    public function publish__event_recorded(): void
    {
        // When
        $t        = Tournament::create();
        $t->publish();

        // Then
        $actual = array_filter($t->pullDomainEvents(), fn(AbstractDomainEvent $e) => $e::eventName() === TournamentPublished::eventName());
        $this->assertNotEmpty($actual);
    }

    /** @test */
    public function publish__already_published__throws_runtime_exception(): void
    {
        // Except
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Tournament must be in preparation status to get published');

        // When
        $t = Tournament::create();
        $t->publish();
        $t->publish();
    }

    /** @test */
    public function signUp__no_participants__returns_participant_id(): void
    {
        // Given
        $expectedCount          = 1;
        $expectedHasParticipant = true;

        // When
        $account = AccountId::create();
        $t       = Tournament::create();
        $t->publish();
        $p = $t->signUp($account);

        // Then
        $this->assertSame($expectedCount, $t->getParticipantCount());
        $this->assertSame($expectedHasParticipant, $t->hasParticipant($p));
    }

    /** @test */
    public function signUp__records_signed_up_event(): void
    {
        // When
        $account = AccountId::create();
        $t       = Tournament::create();
        $t->publish();
        $p = $t->signUp($account);

        // Then
        $actual = array_filter($t->pullDomainEvents(), fn(AbstractDomainEvent $e) => $e::eventName() === ParticipantSignedIn::eventName());
        $this->assertNotEmpty($actual);
    }

    /** @test */
    public function signUp__has_participants(): void
    {
        // Given
        $expectedCount          = 2;
        $expectedHasParticipant = true;

        // When
        $t = Tournament::create();
        $t->publish();
        $p1 = $t->signUp(AccountId::create());
        $p2 = $t->signUp(AccountId::create());

        // Then
        $this->assertSame($expectedCount, $t->getParticipantCount());
        $this->assertSame($expectedHasParticipant, $t->hasParticipant($p1));
        $this->assertSame($expectedHasParticipant, $t->hasParticipant($p1));
    }

    /** @test */
    public function signUp__same_account_two_times__throws_exception(): void
    {
        // Except
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Account already signed up');

        // Given
        $t = Tournament::create();
        $t->publish();
        $account = AccountId::create();

        // When
        $t->signUp($account);
        $t->signUp($account);
    }

    /** @test */
    public function signUp__not_ready_for_sign_ups__throws_exception(): void
    {
        // Except
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Tournament sign up is closed');

        // Given
        $t       = Tournament::create();
        $account = AccountId::create();

        // When
        $t->signUp($account);
    }

    /** @test */
    public function getParticipantCount__has_players__returns_count(): void
    {
        // Given
        $t = Tournament::create();
        $t->publish();
        $expected = 2;

        // When
        $p1 = $t->signUp(AccountId::create());
        $p2 = $t->signUp(AccountId::create());

        // Then
        $this->assertSame($expected, $t->getParticipantCount());
    }

    /** @test */
    public function hasParticipant__exists__returns_true(): void
    {
        // Given
        $t = Tournament::create();
        $t->publish();
        $expected = true;

        // When
        $p1     = $t->signUp(AccountId::create());
        $actual = $t->hasParticipant($p1);

        // Then
        $this->assertSame($expected, $actual);
    }

    /** @test */
    public function hasParticipant__not_exists__returns_false(): void
    {
        // Given
        $t = Tournament::create();
        $t->publish();
        $expected = false;

        // When
        $actual = $t->hasParticipant(ParticipantId::create());

        // Then
        $this->assertSame($expected, $actual);
    }

    /** @test */
    public function signUp__increases_participants_count(): void
    {
        // Given
        $t = Tournament::create();
        $t->publish();
        $expected = 2;

        // When
        $p1 = $t->signUp(AccountId::create());
        $p2 = $t->signUp(AccountId::create());

        // Then
        $this->assertSame($expected, $t->getParticipantCount());
    }

    /** @test */
    public function signUp__has_max_required_participants_exceeded__throws_invalid_argument_exception(): void
    {
        // Expect
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tournament has already full amount of participants');

        // Given
        $expectedCount = 2;

        // When
        $r = new Rules(
            new PlayerCount(
                2,
                $expectedCount
            ), Chip::create(4000), Chip::create(25), Chip::create(50), new Minutes(2)
        );
        $t = Tournament::create($r);
        $t->publish();
        $t->signUp(AccountId::create());
        $t->signUp(AccountId::create());
        $t->signUp(AccountId::create());
    }

    /** @test */
    public function start__is_not_ready__throws_runtime_exception(): void
    {
        // Given
        $reason = 'Tournament is not ready to start';
        $t      = Tournament::create();
        $t->publish();

        // Except
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($reason);

        // When
        $t->signUp(AccountId::create());
        $t->start(TableId::create());
    }

    /** @test */
    public function start__tournament_started_event_recorded(): void
    {
        // Given
        $t = Tournament::create();
        $t->publish();

        // When
        $t->signUp(AccountId::create());
        $t->signUp(AccountId::create());
        $t->start(TableId::create());

        // Then
        $actual = array_filter($t->pullDomainEvents(), fn(AbstractDomainEvent $e) => $e::eventName() === TournamentStarted::eventName());
        $this->assertNotEmpty($actual);
    }

    /** @test */
    public function start__status_changes_to_started(): void
    {
        // Given
        $t = Tournament::create();
        $t->publish();
        $expected = TournamentStatus::STARTED();

        // When
        $t->signUp(AccountId::create());
        $t->signUp(AccountId::create());
        $t->start(TableId::create());

        // Then
        $actual = $t->getStatus();
        $this->assertEquals($expected, $actual);
    }
}
