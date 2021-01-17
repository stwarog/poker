<?php declare(strict_types=1);


namespace Unit\Game\Tournament\Domain;


use App\Account\Domain\AccountId;
use App\Game\Tournament\Domain\Participant;
use App\Game\Tournament\Domain\TournamentId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ParticipantTest extends TestCase
{
    /** @test */
    public function create__initially_has_current_date(): void
    {
        // Given
        $expected = (new DateTimeImmutable())->format('Y-m-d H:i');

        $p = Participant::create(
            AccountId::create(),
            TournamentId::create()
        );

        // When
        $actual = $p->getSignUpDate()->format('Y-m-d H:i');

        // Then
        $this->assertSame($expected, $actual);
    }
}
