<?php declare(strict_types=1);


namespace App\Game\Table\Domain;


use App\Game\Shared\Domain\Cards\CardFactoryInterface;
use App\Game\Shared\Domain\Cards\ShuffleCardsServiceInterface;
use App\Game\Shared\Domain\TableId;
use App\Game\Tournament\Domain\TournamentId;

class TableFactory
{
    private CardFactoryInterface $factory;
    private ShuffleCardsServiceInterface $shuffleCardsService;
    private TableRepositoryInterface $tableRepository;

    public function __construct(
        CardFactoryInterface $factory,
        TableRepositoryInterface $tableRepository,
        ShuffleCardsServiceInterface $shuffleCardsService
    ) {
        $this->factory             = $factory;
        $this->shuffleCardsService = $shuffleCardsService;
        $this->tableRepository     = $tableRepository;
    }

    public function create(TournamentId $tournament): TableId
    {
        $deck = $this->factory->create();
        $this->shuffleCardsService->shuffle($deck);
        $table = Table::create($deck, $tournament);

        $this->tableRepository->save($table);

        return $table->getId();
    }
}
