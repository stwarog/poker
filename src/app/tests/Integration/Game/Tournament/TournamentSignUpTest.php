<?php declare(strict_types=1);


namespace App\Tests\Integration\Game\Tournament;


use App\Game\Tournament\Application\TournamentFacade;
use App\Tests\Integration\IntegrationTest;

class TournamentSignUpTest extends IntegrationTest
{
    private TournamentFacade $facade;

    /** @test */
    public function signUp(): void
    {
        // When
        $t = $this->facade->create(2, 4, true);
        $p = $this->facade->createPlayer();

        $this->facade->signUp(
            (string) $t->getId(),
            (string) $p->getId()
        );

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
