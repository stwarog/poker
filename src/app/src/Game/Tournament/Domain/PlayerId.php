<?php declare(strict_types=1);


namespace App\Game\Tournament\Domain;


use App\Shared\Common\Uuid;

class PlayerId
{
    private Uuid $id;

    public function __construct(Uuid $id)
    {
        $this->id = $id;
    }

    public static function create(): self
    {
        return new self(Uuid::random());
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function equals(self $id): bool
    {
        return $this->id->isEqual($id->id);
    }

    public function notEquals(self $id): bool
    {
        return false === $this->id->isEqual($id->id);
    }

    public function __toString()
    {
        return (string) $this->id;
    }
}
