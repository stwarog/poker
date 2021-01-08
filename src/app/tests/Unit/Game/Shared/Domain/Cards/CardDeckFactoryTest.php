<?php

namespace Unit\Game\Shared\Domain\Cards;

use App\Game\Shared\Domain\Cards\CardDeckFactory;
use PHPUnit\Framework\TestCase;

class CardDeckFactoryTest extends TestCase
{
    /** @test */
    public function create__full_deck_with_unique_52_cards(): void
    {
        // Given
        $expectedCount = 52;

        // When
        $f    = new CardDeckFactory();
        $deck = $f->create();

        // Then
        $this->assertSame($expectedCount, $deck->count());
    }
}
