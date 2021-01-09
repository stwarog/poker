<?php declare(strict_types=1);


namespace App\Tests\Integration\Game\Tournament;


use App\Game\Shared\Domain\Cards\CardCollection;
use App\Game\Shared\Domain\Table;
use App\Game\Tournament\Application\TournamentFacade;
use App\Game\Tournament\Domain\Rules;
use App\Game\Tournament\Domain\Tournament;
use App\Tests\Integration\IntegrationTest;
use Doctrine\ORM\EntityManagerInterface;

class TournamentFacadeTest extends IntegrationTest
{
    private TournamentFacade $facade;

    /** @test */
    public function signUp(): void
    {
        // Given
        $exceptedCount = 1;
        $tournament    = $this->facade->create(2, 4, 4000, 25, 50, true);

        // When
        $this->facade->signUp($tournament);

        // Then
        $this->assertEquals($exceptedCount, $this->getDbCount('tournament_participants'));
    }

    /** @test */
    public function join(): void
    {
        // Given
        $exceptedCount = 2;
        $tournament    = $this->facade->create(2, 4, 4000, 25, 50, true);

        // When
        $player1 = $this->facade->signUp($tournament);
        $player2 = $this->facade->signUp($tournament);
        $this->facade->join($tournament, $player1);
        $this->facade->join($tournament, $player2);

        // Then
        $this->assertEquals($exceptedCount, $this->getDbCount('tournament_participants'));
        $this->assertEquals($exceptedCount, $this->getDbCount('tournament_players'));
    }

    /** @test */
    public function start(): void
    {
        // Given
        $exceptedCardCountPerPlayer = 2;
        $id                         = $this->facade->create(2, 4, 4000, 25, 50, true);

        // When
        $player1 = $this->facade->signUp($id);
        $player2 = $this->facade->signUp($id);
        $this->facade->join($id, $player1);
        $this->facade->join($id, $player2);
        $this->facade->start($id);

        // Then

        $t = $this->facade->get($id);
        foreach ($t->getPlayers() as $player) {
            $this->assertSame($exceptedCardCountPerPlayer, $player->getCards()->count());
        }
//        $this->assertEquals($exceptedCount, $this->getDbCount('tournament_participants'));
//        $this->assertEquals($exceptedCount, $this->getDbCount('tournament_players'));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->beginTransaction();
        $this->facade = $this->c->get(TournamentFacade::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->commit();
    }
}
