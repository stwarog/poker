<?php declare(strict_types=1);

namespace App\Game\Tournament\Domain;

use Symfony\Component\Uid\Uuid;

class Player
{
    /** @var Uuid|null */
    private ?Uuid $uuid;

    public function __construct(?Uuid $uuid = null)
    {
        $this->uuid = $uuid ?? Uuid::v4();
    }

    public function getId(): Uuid
    {
        return $this->uuid;
    }
}
