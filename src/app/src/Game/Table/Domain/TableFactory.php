<?php declare(strict_types=1);


namespace App\Game\Table\Domain;


use App\Game\Shared\Domain\Cards\CardFactoryInterface;
use App\Game\Shared\Domain\Cards\ShuffleCardsServiceInterface;
use App\Game\Tournament\Domain\TournamentId;

class TableFactory
{
    private CardFactoryInterface $factory;
    private ShuffleCardsServiceInterface $shuffleCardsService;

    public function __construct(
        CardFactoryInterface $factory,
        ShuffleCardsServiceInterface $shuffleCardsService
    ) {
        $this->factory             = $factory;
        $this->shuffleCardsService = $shuffleCardsService;
    }

    public function create(TournamentId $tournament): Table
    {
        $deck = $this->factory->create();
        $this->shuffleCardsService->shuffle($deck);

        return Table::create($deck, $tournament);
    }
}
