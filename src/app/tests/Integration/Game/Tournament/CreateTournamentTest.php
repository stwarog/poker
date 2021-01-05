<?php declare(strict_types=1);


namespace App\Tests\Integration\Game\Tournament;


use App\Game\Tournament\Application\TournamentFacade;
use App\Tests\Integration\IntegrationTest;

class CreateTournamentTest extends IntegrationTest
{
    private TournamentFacade $facade;

    /** @test */
    public function create(): void
    {
        // When
        $this->facade->create(2, 4, true);

        // Then
        $q = $this->q()->select('COUNT(*)')->from('tournament');
        $this->assertGreaterThan(0, $q->execute()->fetchOne());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->facade = $this->c->get(TournamentFacade::class);
    }
}
