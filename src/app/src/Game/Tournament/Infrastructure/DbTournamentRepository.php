<?php declare(strict_types=1);


namespace App\Game\Tournament\Infrastructure;


use App\Game\Tournament\Domain\Tournament;
use App\Game\Tournament\Domain\TournamentId;
use App\Game\Tournament\Domain\TournamentRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DbTournamentRepository implements TournamentRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Tournament $tournament): void
    {
        $this->em->persist($tournament);
        $this->em->flush();
    }

    public function getById(TournamentId $tournamentId): Tournament
    {
        $r = $this->em->getRepository(Tournament::class);
        /** @var Tournament $result */
        $result = $r->find($tournamentId);

        return $result;
    }
}
