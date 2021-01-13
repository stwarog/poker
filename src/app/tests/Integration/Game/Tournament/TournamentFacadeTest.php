<?php declare(strict_types=1);


namespace Integration\Game\Tournament;


use App\Game\Tournament\Application\TournamentFacade;
use App\Tests\Integration\IntegrationTest;

class TournamentFacadeTest extends IntegrationTest
{
    private TournamentFacade $facade;

    /** @test */
    public function create(): void
    {
        // Given & When
        $this->facade->create(2, 4, 2000, 25, 50, 2);

        // Then
        $this->assertEquals(1, $this->getDbCount('tournament'));
        $this->assertEquals(0, $this->getDbCount('game_table'));
    }

    /** @test */
    public function signUp(): void
    {
        // Given
        $tournament = $this->facade->create(2, 4, 2000, 25, 50, 2);

        // When
        $this->facade->signUp($tournament);

        // Then
        $this->assertEquals(1, $this->getDbCount('tournament_participants'));
        $this->assertEquals(0, $this->getDbCount('tournament_players'));
        $this->assertEquals(1, $this->getDbCount('tournament'));
        $this->assertEquals(0, $this->getDbCount('game_table'));
    }

    /** @test */
    public function join(): void
    {
        // Given
        $tournament = $this->facade->create(2, 4, 4000, 25, 50, true);

        // When
        $player1 = $this->facade->signUp($tournament);
        $player2 = $this->facade->signUp($tournament);
        $this->facade->join($tournament, $player1);
        $this->facade->join($tournament, $player2);

        // Then
        $this->assertEquals(2, $this->getDbCount('tournament_participants'));
        $this->assertEquals(2, $this->getDbCount('tournament_players'));
        $this->assertEquals(1, $this->getDbCount('tournament'));
        $this->assertEquals(0, $this->getDbCount('game_table'));
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
        $player3 = $this->facade->signUp($id);
        $this->facade->join($id, $player1);
        $this->facade->join($id, $player2);
        $this->facade->join($id, $player3);
        $this->facade->start($id);

        // Then

        $t = $this->facade->get($id);
        foreach ($t->getPlayers() as $player) {
            $this->assertSame($exceptedCardCountPerPlayer, $player->getCards()->count());
        }

//        $this->assertEquals(3, $this->getDbCount('tournament_participants'));
//        $this->assertEquals(3, $this->getDbCount('tournament_players'));
        $this->assertEquals(2, $this->getDbCount('tournament'));
        $this->assertEquals(1, $this->getDbCount('game_table'));
    }

    /** @test */
    public function publish(): void
    {
//        $tournament = $this->facade->create(2, 4, 4000, 25, 50, true);
        $tournament = $this->facade->start('d1e02b1b-c981-450f-9b9a-f58fd4747246');
//        $factory = new CardDeckFactory();
//        $tournament->start(Table::create($factory->create(), $tournament));
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
