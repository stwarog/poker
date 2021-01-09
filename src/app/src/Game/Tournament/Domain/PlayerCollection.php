<?php declare(strict_types=1);


namespace App\Game\Tournament\Domain;


use IteratorAggregate;
use OutOfBoundsException;

class PlayerCollection implements PlayerCollectionInterface
{
    private array $elements;

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    public function removePlayer(PlayerId $player): void
    {
        if (false === $this->hasPlayer($player)) {
            throw new OutOfBoundsException('Unable to find player in collection');
        }
        $filtered       = array_filter($this->elements, fn(Player $p) => $p->getId()->notEquals($player));
        $this->elements = array_values($filtered);
    }

    public function count(): int
    {
        return count($this->elements);
    }

    public function hasPlayer(PlayerId $player): bool
    {
        return !empty(array_filter($this->elements, fn(Player $p) => $p->getId()->equals($player)));
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

    public function valid(): bool
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

    public function addPlayer(Player ...$players): void
    {
        foreach ($players as $player) {
            if ($this->hasPlayer($player->getId())) {
                continue;
            }
            $this->elements[] = $player;
        }
    }

    public function getPlayer(PlayerId $player): ?Player
    {
        $filtered = array_filter($this->elements, fn(Player $p) => $p->getId()->equals($player));
        if (empty($filtered)) {
            return null;
        }

        return reset($filtered);
    }

    public function toArray(): array
    {
        return $this->elements;
    }

    public static function fromCollection(IteratorAggregate $iterable): PlayerCollectionInterface
    {
        $new = new self();

        foreach ($iterable as $player) {
            $new->addPlayer($player);
        }

        return $new;
    }
}