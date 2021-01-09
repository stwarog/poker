<?php declare(strict_types=1);


namespace App\Game\Tournament\Application;


use App\Game\Chip;
use App\Game\Tournament\Domain\PlayerCount;
use App\Game\Tournament\Domain\PlayerId;
use App\Game\Tournament\Domain\Tournament;
use App\Game\Tournament\Domain\TournamentByIdInterface;
use App\Game\Tournament\Domain\TournamentId;
use Exception;

final class TournamentFacade
{
    private CreateTournamentService $createTournamentService;
    private TournamentSignUp $tournamentSignUpService;
    private JoinTournamentService $joinTournamentService;
    private StartTournamentService $startTournamentService;
    private TournamentByIdInterface $repository;

    public function __construct(
        CreateTournamentService $createTournamentService,
        JoinTournamentService $joinTournamentService,
        TournamentSignUp $tournamentSignUpService,
        TournamentByIdInterface $repository,
        StartTournamentService $startTournamentService
    ) {
        $this->createTournamentService = $createTournamentService;
        $this->tournamentSignUpService = $tournamentSignUpService;
        $this->joinTournamentService   = $joinTournamentService;
        $this->startTournamentService  = $startTournamentService;
        $this->repository              = $repository;
    }

    public function create(
        int $minPlayerCount,
        int $maxPlayerCount,
        int $initialChipsPerPlayer,
        int $initialSmallBlind,
        int $initialBigBlind,
        bool $publish = false
    ): string {
        $t = $this->createTournamentService->create(
            new PlayerCount($minPlayerCount, $maxPlayerCount),
            new Chip($initialChipsPerPlayer),
            new Chip($initialSmallBlind),
            new Chip($initialBigBlind),
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

    /**
     * @param string $tournamentId
     *
     * @throws Exception
     */
    public function start(string $tournamentId): void
    {
        $this->startTournamentService->start(
            TournamentId::fromString($tournamentId)
        );
    }

    public function get(string $tournamentId): Tournament
    {
        return $this->repository->getById(TournamentId::fromString($tournamentId));
    }
}
