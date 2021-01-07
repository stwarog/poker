<?php declare(strict_types=1);


namespace App\Unit\Game\Tournament\Domain;


use App\Game\Chip;
use App\Game\Shared\Domain\Cards\Card;
use App\Game\Shared\Domain\Cards\CardCollection;
use App\Game\Shared\Domain\Cards\CardFactoryInterface;
use App\Game\Shared\Domain\Cards\Color;
use App\Game\Shared\Domain\Cards\Value;
use App\Game\Tournament\Domain\PlayerCount;
use App\Game\Tournament\Domain\Rules;
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
        $initialChips      = new Chip(100);
        $initialSmallBlind = new Chip(5);
        $initialBigBlind   = new Chip(10);

        $rules = new Rules(
            new PlayerCount(2, 5),
            $initialChips,
            $initialSmallBlind,
            $initialBigBlind,
        );

        $expectedBigPlayerChips   = new Chip(90);
        $expectedSmallPlayerChips = new Chip(95);

        $t = Tournament::create($rules);
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

        $anySmallBlind = false;
        $anyBigBlind   = false;

        foreach ($t->getPlayers() as $player) {
            $this->assertSame($expectedCardsCount, $player->getCards()->count());

            if ($player->hasSmallBlind()) {
                $this->assertTrue($expectedSmallPlayerChips->equals($player->chipsAmount()));
                $anySmallBlind = true;
            }

            if ($player->hasBigBlind()) {
                $this->assertTrue($expectedBigPlayerChips->equals($player->chipsAmount()));
                $anyBigBlind = true;
            }
        }

        $this->assertTrue($t->deck()->isEmpty());
        $this->assertTrue($anySmallBlind);
        $this->assertTrue($anyBigBlind);
    }
}
