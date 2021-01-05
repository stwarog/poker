<?php declare(strict_types=1);


namespace App\Tests\Integration\Game\Tournament;


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
        // Given

        // When

        // Then
        $this->assertTrue(true);
    }
}
