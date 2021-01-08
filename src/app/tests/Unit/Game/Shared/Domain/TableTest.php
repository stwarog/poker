<?php declare(strict_types=1);


namespace App\Tests\Unit\Game\Shared\Domain;


use App\Game\Chip;
use App\Game\Shared\Domain\Cards\CardCollection;
use App\Game\Shared\Domain\Cards\CardDeckFactory;
use App\Game\Shared\Domain\Table;
use App\Game\Tournament\Domain\Player;
use App\Game\Tournament\Domain\Rules;
use App\Game\Tournament\Domain\Tournament;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    private CardCollection $deck;
    private Tournament $tournament;

    protected function setUp(): void
    {
        parent::setUp();
        $this->deck       = (new CardDeckFactory())->create();
        $this->tournament = Tournament::create();
    }

    /**
     * 1
     * @test
     */
    public function create__table(): void
    {
        // Given
        $deck          = $this->deck;
        $expectedRound = 1;
        $expectedChips = new Chip(0);

        // When
        $t = Table::create($deck, $this->tournament->getRules());

        // Then
        $this->assertSame($expectedRound, $t->getRound());
        $this->assertEquals($expectedChips, $t->getChips());
    }

    /** @test */
    public function pickCard__takes_from_deck_and_returns(): void
    {
        // Given
        $deck              = $this->deck;
        $expectedDeckCount = 50;

        // When
        $t = Table::create($deck, $this->tournament->getRules());
        $t->pickCard(2);

        // Then
        $this->assertSame($expectedDeckCount, $deck->count());
    }

    /** @test */
    public function revealCards__takes_from_deck_and_returns(): void
    {
        // Given
        $deck               = $this->deck;
        $expectedDeckCount  = 49;
        $expectedTakenCount = 3;

        // When
        $t     = Table::create($deck, $this->tournament->getRules());
        $taken = $t->revealCards(3);

        // Then
        $this->assertSame($expectedDeckCount, $deck->count());
        $this->assertSame($expectedTakenCount, $taken->count());
    }

    /** @test */
    public function setCurrentPlayer__makes_turn(): void
    {
        // Given
        $t = Table::create($this->deck, Rules::createDefaults());
        $p = new Player();

        // When
        $t->setCurrentPlayer($p);

        // Then
        $this->assertTrue($p->hasTurn(), 'It is not this player turn');
    }
}
