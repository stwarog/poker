<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;

class Player
{
    private ?PlayerId $uuid;

    public function __construct(?PlayerId $uuid = null)
    {
        $this->uuid = $uuid ?? PlayerId::create();
    }

    public function getId(): PlayerId
    {
        return $this->uuid;
    }
}
