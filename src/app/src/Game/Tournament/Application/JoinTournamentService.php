<?php declare(strict_types=1);

namespace App\Game\Tournament\Application;


use App\Game\Table\Domain\PlayerByIdInterface;
use App\Game\Table\Domain\PlayerId;
use App\Game\Tournament\Domain\TournamentByIdInterface;
use App\Game\Tournament\Domain\TournamentId;
use App\Game\Tournament\Domain\TournamentRepositoryInterface;
use Exception;

class JoinTournamentService
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
     * @param PlayerId     $playerId
     *
     * @throws Exception
     */
    public function join(TournamentId $tournamentId, PlayerId $playerId): void
    {
        $tournament = $this->tournamentRepository->getById($tournamentId);

        $tournament->join($playerId);

        $this->tournamentRepository->save($tournament);
    }
}
