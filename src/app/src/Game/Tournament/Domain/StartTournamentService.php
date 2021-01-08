<?php declare(strict_types=1);


namespace App\Game\Tournament\Domain;


use App\Game\Shared\Domain\Cards\CardFactoryInterface;
use App\Game\Shared\Domain\Cards\ShuffleCardsServiceInterface;

class StartTournamentService
{
    private CardFactoryInterface $factory;
    private ShuffleCardsServiceInterface $shuffleCardsService;

    public function __construct(CardFactoryInterface $factory, ShuffleCardsServiceInterface $shuffleCardsService)
    {
        $this->factory             = $factory;
        $this->shuffleCardsService = $shuffleCardsService;
    }

    public function start(Tournament $tournament): void
    {
        $deck = $this->factory->create();
        $this->shuffleCardsService->shuffle($deck);
        $tournament->start($deck);
    }
}
