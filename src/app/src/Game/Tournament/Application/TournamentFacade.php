<?php declare(strict_types=1);


namespace App\Game\Tournament\Application;


use App\Game\Tournament\Domain\Player;
use App\Game\Tournament\Domain\PlayerCount;
use App\Game\Tournament\Domain\PlayerId;
use App\Game\Tournament\Domain\PlayerRepositoryInterface;
use App\Game\Tournament\Domain\Tournament;
use App\Game\Tournament\Domain\TournamentId;
use Exception;

/**
 * @codeCoverageIgnore
 */
final class TournamentFacade
{
    private CreateTournamentService $createTournamentService;
    private TournamentSignUp $tournamentSignUpService;
    private PlayerRepositoryInterface $playerRepository;

    public function __construct(
        CreateTournamentService $createTournamentService,
        PlayerRepositoryInterface $playerRepository,
        TournamentSignUp $tournamentSignUpService
    ) {
        $this->createTournamentService = $createTournamentService;
        $this->tournamentSignUpService = $tournamentSignUpService;
        $this->playerRepository        = $playerRepository;
    }

    public function create(int $minPlayerCount, int $maxPlayerCount, bool $publish = false): Tournament
    {
        return $this->createTournamentService->create(
            new PlayerCount($minPlayerCount, $maxPlayerCount),
            $publish
        );
    }

    /**
     * @param string $tournamentId
     * @param string $playerId
     *
     * @throws Exception
     */
    public function signUp(string $tournamentId, string $playerId): void
    {
        $this->tournamentSignUpService->signUp(
            TournamentId::fromString($tournamentId),
            PlayerId::fromString($playerId),
        );
    }

    public function createPlayer(): Player
    {
        $p = new Player();
        $this->playerRepository->save($p);

        return $p;
    }
}
