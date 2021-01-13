<?php declare(strict_types=1);


namespace App\Game\Table\Domain;


use IteratorAggregate;
use OutOfBoundsException;

class PlayerCollection implements PlayerCollectionInterface
{
    private array $elements;

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * @param IteratorAggregate $iterable
     *
     * @return static|Player[]
     */
    public static function fromCollection(IteratorAggregate $iterable): PlayerCollectionInterface
    {
        $new = new self();

        foreach ($iterable as $player) {
            $new->addPlayer($player);
        }

        return $new;
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

    public function hasPlayer(PlayerId $player): bool
    {
        return !empty(array_filter($this->elements, fn(Player $p) => $p->getId()->equals($player)));
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

    public function offsetExists($offset): bool
    {
        return isset($this->elements[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->elements[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->elements[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->elements[$offset]);
    }

    /**
     * Should returns only players that participate the current round
     *
     * @return int
     */
    public function getPlayersUnderGameCount(): int
    {
        $wantedDecisions = [PlayerDecision::WAITING, PlayerDecision::CALL, PlayerDecision::RAISE];
        $wantedStatuses  = [PlayerStatus::ACTIVE];

        $filtered = array_filter(
            $this->elements,
            function (Player $p) use ($wantedDecisions, $wantedStatuses) {
                $status   = (string) $p->getStatus();
                $decision = (string) $p->getDecision();

                return in_array($status, $wantedStatuses) && in_array($decision, $wantedDecisions);
            }
        );

        return count($filtered);
    }
}
