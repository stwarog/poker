<?php declare(strict_types=1);


namespace App\Game\Tournament\Domain;


use App\Game\Shared\Domain\Cards\CardFactoryInterface;
use App\Game\Shared\Domain\Cards\ShuffleCardsServiceInterface;
use App\Game\Table\Domain\Table;
use Exception;

class StartTournamentService
{
    private CardFactoryInterface $factory;
    private ShuffleCardsServiceInterface $shuffleCardsService;

    public function __construct(CardFactoryInterface $factory, ShuffleCardsServiceInterface $shuffleCardsService)
    {
        $this->factory             = $factory;
        $this->shuffleCardsService = $shuffleCardsService;
    }

    /**
     * @param Tournament $tournament
     *
     * @throws Exception
     */
    public function start(Tournament $tournament): void
    {
        $deck = $this->factory->create();
        $this->shuffleCardsService->shuffle($deck);
        $tournament->start(Table::create($deck, $tournament));
    }
}
