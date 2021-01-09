<?php declare(strict_types=1);


namespace App\Game\Tournament\Domain;


use Countable;
use Iterator;
use IteratorAggregate;

interface PlayerCollectionInterface extends Iterator, Countable
{
    public function getPlayer(PlayerId $player): ?Player;

    public function count();

    public function addPlayer(Player ...$players): void;

    public function hasPlayer(PlayerId $player): bool;

    public function removePlayer(PlayerId $player): void;

    /**
     * @return Player[]
     */
    public function toArray(): array;

    public static function fromCollection(IteratorAggregate $iterable): self;
}
