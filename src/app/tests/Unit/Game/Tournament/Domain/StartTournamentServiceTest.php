<?php declare(strict_types=1);


namespace App\Unit\Game\Tournament\Domain;


use App\Game\Shared\Domain\Cards\Card;
use App\Game\Shared\Domain\Cards\CardCollection;
use App\Game\Shared\Domain\Cards\CardFactoryInterface;
use App\Game\Shared\Domain\Cards\Color;
use App\Game\Shared\Domain\Cards\Value;
use App\Game\Tournament\Domain\StartTournamentService;
use App\Game\Tournament\Domain\Tournament;
use PHPUnit\Framework\TestCase;

class StartTournamentServiceTest extends TestCase
{
    /**
     * 1
     * @test
     */
    public function start(): void
    {
        // Given
        $t = new Tournament();
        $t->publish();
        $p1 = $t->signUp();
        $p2 = $t->signUp();
        $t->join($p1);
        $t->join($p2);
        $c = new CardCollection(
            [
                new Card(Color::CLUB(), Value::EIGHT()),
                new Card(Color::CLUB(), Value::ACE()),
                new Card(Color::CLUB(), Value::TEN()),
                new Card(Color::CLUB(), Value::THREE()),
            ]
        );

        $expectedCardsCount = 2;

        $factory = $this->createMock(CardFactoryInterface::class);
        $s       = new StartTournamentService($factory);

        $factory
            ->expects($this->once())
            ->method('create')
            ->willReturn($c);

        $this->assertSame(1, $t->getRoundNo());

        // When
        $s->start($t);

        // Then
        $this->assertEquals($c, $t->deck());

        foreach ($t->getPlayers() as $player) {
            $this->assertSame($expectedCardsCount, $player->getCards()->count());
        }

        $this->assertTrue($t->deck()->isEmpty());
    }
}
