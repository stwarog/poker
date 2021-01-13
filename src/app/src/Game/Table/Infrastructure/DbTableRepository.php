<?php declare(strict_types=1);


namespace App\Game\Table\Infrastructure;


use App\Game\Table\Domain\Table;
use App\Game\Table\Domain\TableRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DbTableRepository implements TableRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Table $tournament): void
    {
        $this->em->persist($tournament);
        $this->em->flush();
    }
}
