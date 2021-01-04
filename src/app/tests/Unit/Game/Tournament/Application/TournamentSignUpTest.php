<?php

namespace App\Tests\Unit\Game\Tournament\Application;

use App\Game\Tournament\Application\TournamentSignUp;
use App\Game\Tournament\Domain\PlayerByIdInterface;
use App\Game\Tournament\Domain\TournamentByIdInterface;
use PHPUnit\Framework\TestCase;

class TournamentSignUpTest extends TestCase
{
    /**
     * 1
     * @test
     */
    public function signUp__valid_tournament_and_player(): void
    {
        // Given
        $tournamentId         = 1;
        $playerId             = 1;
        $tournamentRepository = $this->createMock(TournamentByIdInterface::class);
        $tournamentRepository
            ->expects($this->once())
            ->method('getById')
            ->with($tournamentId);

        $playerRepository = $this->createMock(PlayerByIdInterface::class);
        $playerRepository
            ->expects($this->once())
            ->method('getById')
            ->with($playerId);

        // When
        $s = new TournamentSignUp($tournamentRepository, $playerRepository);
        $s->signUp($tournamentId, $playerId);
    }
}
