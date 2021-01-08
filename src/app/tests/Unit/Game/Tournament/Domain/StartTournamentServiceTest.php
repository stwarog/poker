<?php declare(strict_types=1);


namespace App\Unit\Game\Tournament\Domain;


use App\Game\Shared\Domain\Cards\CardCollection;
use App\Game\Shared\Domain\Cards\CardFactoryInterface;
use App\Game\Shared\Domain\Cards\ShuffleCardsServiceInterface;
use App\Game\Shared\Domain\Table;
use App\Game\Tournament\Domain\StartTournamentService;
use App\Game\Tournament\Domain\Tournament;
use PHPUnit\Framework\TestCase;

class StartTournamentServiceTest extends TestCase
{
    /** @test */
    public function start__with_shuffled_deck(): void
    {
        // Given
        $deck = new CardCollection();

        $t = $this->createMock(Tournament::class);
        $t->expects($this->once())
            ->method('start');

        $factory = $this->createMock(CardFactoryInterface::class);
        $factory
            ->expects($this->once())
            ->method('create')
            ->willReturn($deck);

        $shuffle = $this->createMock(ShuffleCardsServiceInterface::class);
        $shuffle
            ->expects($this->once())
            ->method('shuffle');

        // When
        $s = new StartTournamentService($factory, $shuffle);
        $s->start($t);
    }
}
