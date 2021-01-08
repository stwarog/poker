<?php


namespace App\Tests\Unit\Game\Tournament\Domain;


use App\Game\Chip;
use App\Game\Shared\Domain\Cards\Card;
use App\Game\Shared\Domain\Cards\CardCollection;
use App\Game\Shared\Domain\Cards\Color;
use App\Game\Shared\Domain\Cards\Value;
use App\Game\Tournament\Domain\PlayerCount;
use App\Game\Tournament\Domain\PlayerId;
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
    public function signUp__no_participants__returns_participant(): void
    {
        // Given
        $expectedCount          = 1;
        $expectedHasParticipant = true;

        // When
        $t = new Tournament();
        $t->publish();
        $p = $t->signUp();

        // Then
        $this->assertSame($expectedCount, $t->participantCount());
        $this->assertSame($expectedHasParticipant, $t->hasParticipant($p));
    }

    /**
     * N
     * @test
     */
    public function signUp__has_participants__ok(): void
    {
        // Given
        $expectedCount          = 2;
        $expectedHasParticipant = true;

        // When
        $t = new Tournament();
        $t->publish();
        $p1 = $t->signUp();
        $p2 = $t->signUp();

        // Then
        $this->assertSame($expectedCount, $t->participantCount());
        $this->assertSame($expectedHasParticipant, $t->hasParticipant($p1));
        $this->assertSame($expectedHasParticipant, $t->hasParticipant($p1));
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
        $r = new Rules(new PlayerCount(2, $expectedCount), Chip::create(4000), Chip::create(25), Chip::create(50));
        $t = Tournament::create($r);
        $t->publish();
        $t->signUp();
        $t->signUp();
        $t->signUp();
    }

    /** @test */
    public function signUp__tournament_not_ready_for_signups__throws_exception(): void
    {
        // Except
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Tournament sign up is closed');

        // Given
        $expectedCount = 2;

        // When
        $r = Rules::createDefaults();
        $t = Tournament::create($r);
        $t->signUp();
        $t->signUp();
        $t->startTournament();
        $t->signUp();
    }

    /** @test */
    public function join__not_signed_up__throws_runtime_exception(): void
    {
        // Except
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Can not join this tournament because is not signed up');

        // When
        $t = new Tournament();
        $t->publish();
        $t->join(PlayerId::create());
    }

    /** @test */
    public function join__already_joined__throws_runtime_exception(): void
    {
        // Except
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Player already joined to this tournament');

        // When
        $t = new Tournament();
        $t->publish();
        $p1 = $t->signUp();
        $t->signUp();
        $t->join($p1);
        $t->join($p1);
    }

    /**
     * 1
     * @test
     */
    public function join__player_receives_rule_initial_chip_amount(): void
    {
        // Given
        $r = Rules::createDefaults();

        $expected             = true;
        $expectedPlayerCounts = 2;
        $expectedChipAmount   = $r->getInitialChipsPerPlayer();

        // When
        $t = Tournament::create($r);
        $t->publish();
        $p1 = $t->signUp();
        $p2 = $t->signUp();
        $t->join($p1);
        $t->join($p2);

        // Then
        $this->assertSame($expected, $t->hasPlayer($p1));
        $this->assertSame($expected, $t->hasPlayer($p2));
        $this->assertSame($expectedPlayerCounts, $t->getPlayersCount());
        $this->assertTrue($expectedChipAmount->equals($t->getPlayerChips($p1)));
        $this->assertTrue($expectedChipAmount->equals($t->getPlayerChips($p2)));
    }

    /** @test */
    public function join__minimal_players_count__changes_status_to_ready_to_start(): void
    {
        // Given
        $expected = TournamentStatus::READY();

        // When
        $t = new Tournament();
        $t->publish();
        $p1 = $t->signUp();
        $p2 = $t->signUp();
        $t->join($p1);
        $t->join($p2);

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
        $expected = false;

        // When
        $t = new Tournament();
        $t->publish();
        $p1 = $t->signUp();
        $p2 = $t->signUp();
        $t->join($p1);
        $t->join($p2);
        $t->leave($p1);

        // Then
        $this->assertSame($expected, $t->hasPlayer($p1));
    }

    /** @test */
    public function leave__not_joined__throws_invalid_argument_exception(): void
    {
        // Except
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Player is already out of this tournament');

        // When
        $t = new Tournament();
        $t->publish();
        $p1 = $t->signUp();
        $p2 = $t->signUp();
        $t->join($p1);
        $t->join($p2);
        $t->leave($p1);
        $t->leave($p1);
    }

    /** @test */
    public function start__not_ready__throws_runtime_exception(): void
    {
        // Except
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Tournament is not ready to start');

        // When
        $t = new Tournament();
        $t->publish();
        $p = $t->signUp();
        $t->join($p);
        $t->start(new CardCollection());
    }

    /**
     * 1
     * @test
     */
    public function start__blinds_and_two_cards_assigned_each_player(): void
    {
        // Given
        $initialChips      = new Chip(100);
        $initialSmallBlind = new Chip(5);
        $initialBigBlind   = new Chip(10);

        $rules = new Rules(
            new PlayerCount(2, 5),
            $initialChips,
            $initialSmallBlind,
            $initialBigBlind,
        );

        $expectedBigPlayerChips   = new Chip(90);
        $expectedSmallPlayerChips = new Chip(95);

        $t = Tournament::create($rules);
        $t->publish();

        $p1 = $t->signUp();
        $p2 = $t->signUp();
        $t->join($p1);
        $t->join($p2);

        $deck = new CardCollection(
            [
                new Card(Color::CLUB(), Value::EIGHT()),
                new Card(Color::CLUB(), Value::ACE()),
                new Card(Color::CLUB(), Value::TEN()),
                new Card(Color::CLUB(), Value::THREE()),
            ]
        );

        $expectedCardsCount = 2;

        $this->assertSame(1, $t->getRoundNo());

        // When
        $t->start($deck);

        // Then
        $this->assertEquals($deck, $t->deck());

        $anySmallBlind = false;
        $anyBigBlind   = false;

        foreach ($t->getPlayers() as $player) {
            $this->assertSame($expectedCardsCount, $player->getCards()->count());

            if ($player->hasSmallBlind()) {
                $this->assertTrue($expectedSmallPlayerChips->equals($player->chipsAmount()));
                $anySmallBlind = true;
            }

            if ($player->hasBigBlind()) {
                $this->assertTrue($expectedBigPlayerChips->equals($player->chipsAmount()));
                $anyBigBlind = true;
            }
        }

        $this->assertTrue($t->deck()->isEmpty());
        $this->assertTrue($anySmallBlind);
        $this->assertTrue($anyBigBlind);
    }
}
