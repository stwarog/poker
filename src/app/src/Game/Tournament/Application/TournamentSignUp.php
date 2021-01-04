<?php declare(strict_types=1);

namespace App\Game\Tournament\Application;


use App\Game\Tournament\Domain\PlayerByIdInterface;
use App\Game\Tournament\Domain\TournamentByIdInterface;
use App\Game\Tournament\Domain\TournamentSpecificationInterface;

class TournamentSignUp
{
    private TournamentByIdInterface $tournamentRepository;
    private PlayerByIdInterface $playerRepository;
    private TournamentSpecificationInterface $signUpSpecification;

    public function __construct(
        TournamentByIdInterface $tournamentRepository,
        PlayerByIdInterface $playerRepository,
        TournamentSpecificationInterface $signUpSpecification
    ) {
        $this->tournamentRepository = $tournamentRepository;
        $this->playerRepository     = $playerRepository;
        $this->signUpSpecification  = $signUpSpecification;
    }

    public function signUp(string $tournamentId, string $playerId): void
    {
        $tournament = $this->tournamentRepository->getById($tournamentId);
        $player     = $this->playerRepository->getById($playerId);

        $tournament->signUp($player, $this->signUpSpecification);
    }
}
