<?php declare(strict_types=1);


namespace App\Game\Tournament\Application;


use App\Account\Domain\AccountId;
use App\Game\Shared\Domain\Chip;
use App\Game\Table\Domain\PlayerId;
use App\Game\Tournament\Domain\PlayerCount;
use App\Game\Tournament\Domain\Tournament;
use App\Game\Tournament\Domain\TournamentByIdInterface;
use App\Game\Tournament\Domain\TournamentId;
use App\Shared\Domain\Minutes;
use Exception;

final class TournamentFacade
{
    private CreateTournamentService $createTournamentService;
    private TournamentSignUp $tournamentSignUpService;
    private JoinTournamentService $joinTournamentService;
    private StartTournamentService $startTournamentService;
    private TournamentByIdInterface $repository;
    private TournamentDecisionService $decisionService;
    private TableViewRepositoryInterface $tableViewRepository;

    public function __construct(
        CreateTournamentService $createTournamentService,
        JoinTournamentService $joinTournamentService,
        TournamentSignUp $tournamentSignUpService,
        TournamentByIdInterface $repository,
        TournamentDecisionService $decisionService,
        TableViewRepositoryInterface $tableViewRepository,
        StartTournamentService $startTournamentService
    ) {
        $this->createTournamentService = $createTournamentService;
        $this->tournamentSignUpService = $tournamentSignUpService;
        $this->joinTournamentService   = $joinTournamentService;
        $this->startTournamentService  = $startTournamentService;
        $this->repository              = $repository;
        $this->decisionService         = $decisionService;
        $this->tableViewRepository     = $tableViewRepository;
    }

    public function create(
        int $minPlayerCount,
        int $maxPlayerCount,
        int $initialChipsPerPlayer,
        int $initialSmallBlind,
        int $initialBigBlind,
        int $blindsChangeInterval = 2,
        bool $publish = false
    ): string {
        $t = $this->createTournamentService->create(
            new PlayerCount($minPlayerCount, $maxPlayerCount),
            new Chip($initialChipsPerPlayer),
            new Chip($initialSmallBlind),
            new Chip($initialBigBlind),
            new Minutes($blindsChangeInterval),
            $publish
        );

        return $t->toString();
    }

    /**
     * @param string $tournamentId
     * @param string $accountId
     *
     * @return string
     * @throws Exception
     */
    public function signUp(string $tournamentId, string $accountId): string
    {
        $participate = $this->tournamentSignUpService->signUp(
            TournamentId::fromString($tournamentId),
            AccountId::fromString($accountId),
        );

        return $participate->toString();
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

    /**
     * @param string $tournamentId
     * @param string $playerId
     *
     * @throws Exception
     */
    public function fold(string $tournamentId, string $playerId): void
    {
        $this->decisionService->fold(
            TournamentId::fromString($tournamentId),
            PlayerId::fromString($playerId),
        );
    }

    /**
     * @param string $tournamentId
     * @param string $playerId
     *
     * @throws Exception
     */
    public function call(string $tournamentId, string $playerId): void
    {
        $this->decisionService->call(
            TournamentId::fromString($tournamentId),
            PlayerId::fromString($playerId),
        );
    }

    /**
     * @param string $tournamentId
     * @param string $playerId
     *
     * @throws Exception
     */
    public function allIn(string $tournamentId, string $playerId): void
    {
        $this->decisionService->allIn(
            TournamentId::fromString($tournamentId),
            PlayerId::fromString($playerId),
        );
    }

    /**
     * @param string $tournamentId
     * @param string $playerId
     * @param int    $amount
     *
     * @throws Exception
     */
    public function raise(string $tournamentId, string $playerId, int $amount): void
    {
        $this->decisionService->raise(
            TournamentId::fromString($tournamentId),
            PlayerId::fromString($playerId),
            new Chip($amount)
        );
    }

    public function get(string $tournamentId): Tournament
    {
        return $this->repository->getById(TournamentId::fromString($tournamentId));
    }

    public function getTableView(string $tournamentId): ?TableView
    {
//        $t = $this->repository->getById(TournamentId::fromString($tournamentId));
//
//        if (empty($t->getTable())) {
//            return null;
//        }
//
//        return $this->tableViewRepository->getById($t->getTable());
    }
}
