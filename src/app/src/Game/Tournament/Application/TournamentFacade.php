<?php declare(strict_types=1);


namespace App\Game\Tournament\Application;


use App\Game\Tournament\Domain\PlayerCount;
use App\Game\Tournament\Domain\Tournament;

/**
 * @codeCoverageIgnore
 */
final class TournamentFacade
{
    private CreateTournamentService $createTournamentService;

    public function __construct(CreateTournamentService $createTournamentService)
    {
        $this->createTournamentService = $createTournamentService;
    }

    public function create(int $minPlayerCount, int $maxPlayerCount): Tournament
    {
        return $this->createTournamentService->create(
            new PlayerCount($minPlayerCount, $maxPlayerCount)
        );
    }
}
