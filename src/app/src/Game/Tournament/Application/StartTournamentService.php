<?php declare(strict_types=1);

namespace App\Game\Tournament\Application;


use App\Game\Tournament\Domain\StartTournamentService as DomainStartTournamentService;
use App\Game\Tournament\Domain\TournamentId;
use App\Game\Tournament\Domain\TournamentRepositoryInterface;
use Exception;

class StartTournamentService
{
    private TournamentRepositoryInterface $repository;
    private DomainStartTournamentService $service;

    public function __construct(
        TournamentRepositoryInterface $repository,
        DomainStartTournamentService $service
    ) {
        $this->repository = $repository;
        $this->service    = $service;
    }

    /**
     * @param TournamentId $tournamentId
     *
     * @throws Exception
     */
    public function start(TournamentId $tournamentId): void
    {
        $tournament = $this->repository->getById($tournamentId);

        $this->service->start($tournament);

        $this->repository->save($tournament);
    }
}
