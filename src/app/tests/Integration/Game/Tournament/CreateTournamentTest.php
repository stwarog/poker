<?php declare(strict_types=1);


namespace App\Tests\Integration\Game\Tournament;


use App\Game\Tournament\Domain\Player;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateTournamentTest extends KernelTestCase
{
    private EntityManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /** @test */
    public function create(): void
    {
//        $player = new Player();
//        $this->entityManager->persist($player);
//        $this->entityManager->flush();
        $repo   = $this->entityManager->getRepository(Player::class);
        $player = $repo->find('93383c90-13de-4caf-b138-16e754acb8da');
        dump($player);
        // Given

        // When

        // Then
        $this->assertTrue(true);
    }
}
