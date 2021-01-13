<?php declare(strict_types=1);

namespace App\Game\Tournament\Application;


use App\Game\Shared\Domain\Chip;
use App\Game\Tournament\Domain\PlayerByIdInterface;
use App\Game\Tournament\Domain\PlayerId;
use App\Game\Tournament\Domain\TournamentByIdInterface;
use App\Game\Tournament\Domain\TournamentId;
use App\Game\Tournament\Domain\TournamentRepositoryInterface;
use Exception;

class TournamentDecisionService
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
    public function fold(TournamentId $tournamentId, PlayerId $playerId): void
    {
        $tournament = $this->tournamentRepository->getById($tournamentId);

        $tournament->fold($playerId);

        $this->tournamentRepository->save($tournament);
    }

    /**
     * @param TournamentId $tournamentId
     * @param PlayerId     $playerId
     *
     * @throws Exception
     */
    public function call(TournamentId $tournamentId, PlayerId $playerId): void
    {
        $tournament = $this->tournamentRepository->getById($tournamentId);

        $tournament->call($playerId);

        $this->tournamentRepository->save($tournament);
    }

    /**
     * @param TournamentId $tournamentId
     * @param PlayerId     $playerId
     *
     * @throws Exception
     */
    public function allIn(TournamentId $tournamentId, PlayerId $playerId): void
    {
        $tournament = $this->tournamentRepository->getById($tournamentId);

        $tournament->allIn($playerId);

        $this->tournamentRepository->save($tournament);
    }

    /**
     * @param TournamentId $tournamentId
     * @param PlayerId     $playerId
     * @param Chip         $amount
     *
     * @throws Exception
     */
    public function raise(TournamentId $tournamentId, PlayerId $playerId, Chip $amount): void
    {
        $tournament = $this->tournamentRepository->getById($tournamentId);

        $tournament->raise($playerId, $amount);

        $this->tournamentRepository->save($tournament);
    }
}
