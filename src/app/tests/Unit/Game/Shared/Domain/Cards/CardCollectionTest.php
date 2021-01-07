<?php

namespace Unit\Game\Shared\Domain\Cards;

use App\Game\Shared\Domain\Cards\Card;
use App\Game\Shared\Domain\Cards\CardCollection;
use App\Game\Shared\Domain\Cards\Color;
use App\Game\Shared\Domain\Cards\Value;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

class CardCollectionTest extends TestCase
{
    /** @test */
    public function add__card(): void
    {
        // Given
        $card = new Card(Color::CLUB(), Value::EIGHT());

        // When
        $c = new CardCollection();
        $c->addCard($card);

        // Then
        $this->assertTrue($c->hasCard($card));
    }

    /** @test */
    public function remove__card(): void
    {
        // Given
        $card = new Card(Color::CLUB(), Value::EIGHT());

        // When
        $c = new CardCollection();
        $c->addCard($card);
        $c->removeCard($card);

        // Then
        $this->assertTrue($c->isEmpty());
    }

    /**
     * 1
     * @test
     */
    public function pickCard__has_cards__returns_card_and_removes_from_stock(): void
    {
        // Given
        $card = new Card(Color::CLUB(), Value::EIGHT());

        // When
        $c = new CardCollection();
        $c->addCard($card);

        $new = $c->pickCard();

        // Then
        $this->assertTrue($c->isEmpty());
        $this->assertTrue($new->hasCard($card));
    }

    /**
     * N
     * @test
     */
    public function pickCard_many__has_cards__returns_card_and_removes_from_stock(): void
    {
        // Given
        $card1 = new Card(Color::CLUB(), Value::EIGHT());
        $card2 = new Card(Color::CLUB(), Value::FIVE());

        // When
        $c = new CardCollection();
        $c->addCard($card1);
        $c->addCard($card2);

        $new = $c->pickCard(2);

        // Then
        $this->assertTrue($c->isEmpty());
        $this->assertTrue($new->hasCard($card1));
        $this->assertTrue($new->hasCard($card2));
    }

    /** @test */
    public function pickCard__no_more_cards__throws_out_of_bound_exception(): void
    {
        // Except
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('There is no more cards');

        // When
        $c = new CardCollection();
        $c->pickCard();
    }
}
