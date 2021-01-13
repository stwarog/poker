<?php declare(strict_types=1);


namespace App\Game\Tournament\Domain;


use App\Game\Shared\Domain\Cards\CardFactoryInterface;
use App\Game\Shared\Domain\Cards\ShuffleCardsServiceInterface;
use App\Game\Table\Domain\Table;
use App\Game\Table\Domain\TableFactory;
use Exception;

class StartTournamentService
{
    private TableFactory $tableFactory;
    private TournamentRepositoryInterface $tournamentRepository;

    public function __construct(
        TableFactory $tableFactory,
        TournamentRepositoryInterface $tournamentRepository
    ) {
        $this->tableFactory         = $tableFactory;
        $this->tournamentRepository = $tournamentRepository;
    }

    public function start(TournamentId $tournamentId): void
    {
        $table = $this->tableFactory->create($tournamentId);

        $tournament = $this->tournamentRepository->getById($tournamentId);
        $tournament->start($table);

        $this->tournamentRepository->save($tournament);
    }
}
