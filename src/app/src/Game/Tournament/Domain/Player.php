<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;

use App\Shared\Common\Uuid;

class Player
{
    private string $uuid;

    public function __construct(?PlayerId $uuid = null)
    {
        $this->uuid = $uuid ? (string) $uuid : (string) PlayerId::create();
    }

    public function getId(): PlayerId
    {
        return new PlayerId(Uuid::from($this->uuid));
    }
}
