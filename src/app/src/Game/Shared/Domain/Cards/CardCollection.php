<?php declare(strict_types=1);


namespace App\Game\Shared\Domain\Cards;


use Doctrine\Common\Collections\ArrayCollection;
use OutOfBoundsException;

class CardCollection extends ArrayCollection implements CardCollectionInterface
{
    public function addCard(Card ...$cards): void
    {
        foreach ($cards as $card) {
            $this->add($card);
        }
    }

    public function removeCard(Card $card): void
    {
        $this->removeElement($card);
    }

    public function pickCard(int $count = 1): self
    {
        if ($count > $this->count()) {
            throw new OutOfBoundsException('There is no more cards');
        }

        $elements = $this->slice(0, $count);
        for ($c = 0; $c !== $count; $c++) {
            $this->remove($c);
        }

        return new self($elements);
    }

    public function hasCard(Card $card): bool
    {
        return $this->contains($card);
    }
}
