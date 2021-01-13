<?php declare(strict_types=1);


namespace App\Shared\Domain;


abstract class AbstractId
{
    private Uuid $id;

    public function __construct(Uuid $id)
    {
        $this->id = $id;
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public static function fromString(string $value): self
    {
        return new static(new Uuid($value));
    }

    /**
     * @return static
     */
    public static function create(): self
    {
        return new static(Uuid::random());
    }

    public function equals(self $id): bool
    {
        return $this->id->isEqual($id->id);
    }

    public function notEquals(self $id): bool
    {
        return false === $this->id->isEqual($id->id);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return (string) $this->id;
    }
}
