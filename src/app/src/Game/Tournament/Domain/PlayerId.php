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

    public function id(): Uuid
    {
        return $this->id;
    }

    public static function create(): self
    {
        return new self(Uuid::random());
    }

    public function equals(self $id): bool
    {
        return $this->id->isEqual($id->id);
    }

    public function __toString()
    {
        return (string)$this->id;
    }
}
