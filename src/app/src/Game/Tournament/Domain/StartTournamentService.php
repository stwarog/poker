<?php declare(strict_types=1);


namespace App\Game\Tournament\Domain;


use App\Game\Shared\Domain\Cards\CardFactoryInterface;

class StartTournamentService
{
    private CardFactoryInterface $factory;

    public function __construct(CardFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function start(Tournament $tournament): void
    {
        $cards = $this->factory->create();
        $tournament->start($cards);

        $players = $tournament->getPlayers();

        $players[0]->giveSmallBlind($tournament);
        $players[1]->giveBigBlind($tournament);

        foreach ($players as $player) {
            $player->pickCards($tournament, 2);
        }
    }
}
