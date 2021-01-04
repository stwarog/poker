<?php declare(strict_types=1);

namespace App\Game\Tournament\Application;


use App\Game\Tournament\Domain\PlayerByIdInterface;
use App\Game\Tournament\Domain\TournamentByIdInterface;

class TournamentSignUp
{
    private TournamentByIdInterface $tournamentRepository;
    private PlayerByIdInterface $playerRepository;

    public function __construct(
        TournamentByIdInterface $tournamentRepository,
        PlayerByIdInterface $playerRepository
    ) {
        $this->tournamentRepository = $tournamentRepository;
        $this->playerRepository     = $playerRepository;
    }

    public function signUp(string $tournamentId, string $playerId): void
    {
        $tournament = $this->tournamentRepository->getById($tournamentId);
        $player     = $this->playerRepository->getById($playerId);

        $tournament->signUp($player);
    }
}
