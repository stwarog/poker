<?php declare(strict_types=1);


namespace App\Game\Shared\Domain\Cards;


use Iterator;
use OutOfBoundsException;

class CardCollection implements CardCollectionInterface, Iterator
{
    private array $elements;

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    public function addCard(Card ...$cards): void
    {
        foreach ($cards as $card) {
            $this->elements[] = $card;
        }
    }

    public function removeCard(Card $card): void
    {
        $this->elements = array_filter($this->elements, fn(Card $c) => (string) $c !== (string) $card);
    }

    public function pickCard(int $amount = 1): self
    {
        if ($amount > $this->count()) {
            throw new OutOfBoundsException('There is no more cards');
        }

        $new            = array_splice($this->elements, 0, $amount);
        $this->elements = array_values($this->elements);

        return new self($new);
    }

    public function count(): int
    {
        return count($this->elements);
    }

    public function hasCard(Card $card): bool
    {
        return !empty(array_filter($this->elements, fn(Card $c) => (string) $c === (string) $card));
    }

    public function current()
    {
        return current($this->elements);
    }

    public function next()
    {
        next($this->elements);
    }

    public function key()
    {
        return key($this->elements);
    }

    public function valid()
    {
        return key($this->elements) !== null;
    }

    public function rewind()
    {
        reset($this->elements);
    }

    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    public function getKeys(): array
    {
        return array_keys($this->elements);
    }

    public function addCards(CardCollection $cards): void
    {
        foreach ($cards as $card) {
            $this->addCard($card);
        }
    }
}
