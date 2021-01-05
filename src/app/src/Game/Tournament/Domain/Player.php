<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;

class Player
{
    private string $id;

    public function __construct(?PlayerId $uuid = null)
    {
        $this->id = $uuid ? (string) $uuid : (string) PlayerId::create();
    }

    public function getId(): PlayerId
    {
        return PlayerId::fromString($this->id);
    }
}
