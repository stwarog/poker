<?php declare(strict_types=1);


namespace App\Game\Tournament\Infrastructure;


use App\Game\Table\Domain\Player;
use App\Game\Table\Domain\PlayerByIdInterface;
use App\Game\Table\Domain\PlayerId;
use App\Game\Table\Domain\PlayerRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DbPlayerRepository implements PlayerByIdInterface, PlayerRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Player $player): void
    {
        $this->em->persist($player);
        $this->em->flush();
    }

    public function getById(PlayerId $playerId): Player
    {
        $r = $this->em->getRepository(Player::class);
        /** @var Player $result */
        $result = $r->find($playerId);

        return $result;
    }
}
