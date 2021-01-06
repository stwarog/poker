<?php declare(strict_types=1);


namespace App\Game\Tournament\Application;


use App\Game\Tournament\Domain\PlayerCount;
use App\Game\Tournament\Domain\PlayerId;
use App\Game\Tournament\Domain\TournamentId;
use Exception;

/**
 * @codeCoverageIgnore
 */
final class TournamentFacade
{
    private CreateTournamentService $createTournamentService;
    private TournamentSignUp $tournamentSignUpService;
    private JoinTournamentService $joinTournamentService;

    public function __construct(
        CreateTournamentService $createTournamentService,
        JoinTournamentService $joinTournamentService,
        TournamentSignUp $tournamentSignUpService
    ) {
        $this->createTournamentService = $createTournamentService;
        $this->tournamentSignUpService = $tournamentSignUpService;
        $this->joinTournamentService   = $joinTournamentService;
    }

    public function create(int $minPlayerCount, int $maxPlayerCount, bool $publish = false): string
    {
        $t = $this->createTournamentService->create(
            new PlayerCount($minPlayerCount, $maxPlayerCount),
            $publish
        );

        return $t->toString();
    }

    /**
     * @param string $tournamentId
     *
     * @return string
     * @throws Exception
     */
    public function signUp(string $tournamentId): string
    {
        $player = $this->tournamentSignUpService->signUp(
            TournamentId::fromString($tournamentId)
        );

        return $player->toString();
    }

    /**
     * @param string $tournamentId
     * @param string $playerId
     *
     * @throws Exception
     */
    public function join(string $tournamentId, string $playerId): void
    {
        $this->joinTournamentService->join(
            TournamentId::fromString($tournamentId),
            PlayerId::fromString($playerId),
        );
    }
}
