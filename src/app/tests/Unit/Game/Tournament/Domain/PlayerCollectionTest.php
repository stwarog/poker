<?php declare(strict_types=1);


namespace App\Tests\Unit\Game\Tournament\Domain;


use App\Game\Tournament\Domain\Player;
use App\Game\Tournament\Domain\PlayerCollection;
use App\Game\Tournament\Domain\PlayerDecision;
use App\Game\Tournament\Domain\PlayerStatus;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

class PlayerCollectionTest extends TestCase
{
    /** @test */
    public function create__new_collection__is_empty(): void
    {
        // When & Then
        $c = new PlayerCollection();
        $this->assertTrue($c->isEmpty());
        $this->assertSame(0, count($c));
    }

    /** @test */
    public function create__with_elements(): void
    {
        // When & Then
        $c = new PlayerCollection([new Player(), new Player()]);
        $this->assertFalse($c->isEmpty());
        $this->assertSame(2, count($c));
    }

    /** @test */
    public function removePlayer__player_exists(): void
    {
        // Given
        $p = new Player();
        $c = new PlayerCollection([$p]);

        // When
        $c->removePlayer($p->getId());

        // Then
        $this->assertTrue($c->isEmpty());
    }

    /** @test */
    public function removePlayer__player_not_exists__throws_out_of_bound_exception(): void
    {
        // Except
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Unable to find player in collection');

        // Given
        $p = new Player();
        $c = new PlayerCollection();

        // When
        $c->removePlayer($p->getId());
    }

    /**
     * 1
     * @test
     */
    public function addPlayer__not_dupes(): void
    {
        // Given
        $p = new Player();

        // When
        $c = new PlayerCollection();
        $c->addPlayer($p);

        // Then
        $this->assertTrue($c->hasPlayer($p->getId()));
    }

    /** @test */
    public function addPlayer_many__no_dupes(): void
    {
        // Given
        $p1 = new Player();
        $p2 = new Player();

        // When
        $c = new PlayerCollection();
        $c->addPlayer($p1);
        $c->addPlayer($p2);

        // Then
        $this->assertTrue($c->hasPlayer($p1->getId()));
        $this->assertTrue($c->hasPlayer($p2->getId()));
    }

    /** @test */
    public function addPlayer__few_time_the_same__adds_once(): void
    {
        // Given
        $p1 = new Player();

        // When
        $c = new PlayerCollection();
        $c->addPlayer($p1);
        $c->addPlayer($p1);

        // Then
        $this->assertTrue($c->hasPlayer($p1->getId()));
        $this->assertSame(1, count($c));
    }

    /** @test */
    public function getPlayer__player_exists(): void
    {
        // Given
        $p1 = new Player();
        $p2 = new Player();

        // When
        $c = new PlayerCollection();
        $c->addPlayer($p1);
        $c->addPlayer($p2);

        // Then
        $this->assertSame($p1, $c->getPlayer($p1->getId()));
        $this->assertSame($p2, $c->getPlayer($p2->getId()));
    }

    /** @test */
    public function toArray__one_key_removed__returns_always_reordered(): void
    {
        // Given
        $expectedKeys = [0, 1];

        // When
        $p1 = new Player();
        $p2 = new Player();
        $p3 = new Player();
        $c  = new PlayerCollection();
        $c->addPlayer($p1, $p2, $p3);
        $c->removePlayer($p2->getId());
        $actual = array_keys($c->toArray());

        // Then
        $this->assertSame($expectedKeys, $actual);
    }

    /**
     * @test
     * @dataProvider getPlayersUnderGameCountDataProvider
     *
     * @param PlayerStatus   $status
     * @param PlayerDecision $decision
     */
    public function getPlayersUnderGameCount(PlayerDecision $decision, PlayerStatus $status): void
    {
        // Given
        $c = new PlayerCollection();
        $p = $this->createMock(Player::class);
        $p->method('getStatus')->willReturn($status);
        $p->method('getDecision')->willReturn($decision);
        $c->addPlayer($p);

        // When
        $actual = $c->getPlayersUnderGameCount();

        // Then
        $this->assertSame(1, $actual);
    }

    public function getPlayersUnderGameCountDataProvider(): array
    {
        return [
            'WAITING decision and active status' => [
                PlayerDecision::WAITING(),
                PlayerStatus::ACTIVE(),
            ],
            'CALL decision and active status'    => [
                PlayerDecision::CALL(),
                PlayerStatus::ACTIVE(),
            ],
            'RAISE decision and active status'   => [
                PlayerDecision::RAISE(),
                PlayerStatus::ACTIVE(),
            ],
        ];
    }
}
