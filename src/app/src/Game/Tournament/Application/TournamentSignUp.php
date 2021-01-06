<?php declare(strict_types=1);

namespace App\Game\Tournament\Application;


use App\Game\Tournament\Domain\PlayerByIdInterface;
use App\Game\Tournament\Domain\PlayerId;
use App\Game\Tournament\Domain\TournamentByIdInterface;
use App\Game\Tournament\Domain\TournamentId;
use App\Game\Tournament\Domain\TournamentRepositoryInterface;
use Exception;

class TournamentSignUp
{
    private TournamentRepositoryInterface $tournamentRepository;
    private PlayerByIdInterface $playerRepository;

    public function __construct(
        TournamentByIdInterface $tournamentRepository,
        PlayerByIdInterface $playerRepository
    ) {
        $this->tournamentRepository = $tournamentRepository;
        $this->playerRepository     = $playerRepository;
    }

    /**
     * @param TournamentId $tournamentId
     *
     * @return PlayerId
     * @throws Exception
     */
    public function signUp(TournamentId $tournamentId): PlayerId
    {
        $tournament = $this->tournamentRepository->getById($tournamentId);

        $player = $tournament->signUp();

        $this->tournamentRepository->save($tournament);

        return $player;
    }
}
