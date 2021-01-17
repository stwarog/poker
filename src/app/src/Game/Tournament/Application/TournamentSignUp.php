<?php declare(strict_types=1);

namespace App\Game\Tournament\Application;


use App\Account\Domain\AccountId;
use App\Game\Table\Domain\PlayerByIdInterface;
use App\Game\Tournament\Domain\ParticipantId;
use App\Game\Tournament\Domain\TournamentByIdInterface;
use App\Game\Tournament\Domain\TournamentId;
use App\Game\Tournament\Domain\TournamentRepositoryInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use Exception;

class TournamentSignUp
{
    private TournamentRepositoryInterface $tournamentRepository;
    private PlayerByIdInterface $playerRepository;
    private EventBusInterface $bus;

    public function __construct(
        TournamentByIdInterface $tournamentRepository,
        PlayerByIdInterface $playerRepository,
        EventBusInterface $bus
    ) {
        $this->tournamentRepository = $tournamentRepository;
        $this->playerRepository     = $playerRepository;
        $this->bus                  = $bus;
    }

    /**
     * @param TournamentId $tournament
     *
     * @param AccountId    $account
     *
     * @return ParticipantId
     * @throws Exception
     */
    public function signUp(TournamentId $tournament, AccountId $account): ParticipantId
    {
        $t = $this->tournamentRepository->getById($tournament);

        $participant = $t->signUp($account);

        $this->tournamentRepository->save($t);

        $this->bus->publish(...$t->pullDomainEvents());

        return $participant;
    }
}
