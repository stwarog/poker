<?php declare(strict_types=1);


namespace App\Shared\Domain;


use DateTimeImmutable;

abstract class AbstractDomainEvent
{
    private string $eventId;
    private string $occurredOn;
    private string $aggregateId;
    private array $body;

    public function __construct(string $aggregateId, array $body = [])
    {
        $this->eventId     = (string) Uuid::random();
        $this->occurredOn  = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $this->aggregateId = $aggregateId;
        $this->body        = $body;
    }

    public static function createEmpty(string $aggregateId, array $body = []): self
    {
        return new static($aggregateId, $body);
    }

    abstract public static function eventName(): string;

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    public function occurredOn(): string
    {
        return $this->occurredOn;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function toArray(): array
    {
        return [
            'name'        => static::eventName(),
            'aggregateId' => $this->aggregateId,
            'eventId'     => $this->eventId,
            'occurredOn'  => $this->occurredOn,
            'body'        => $this->body,
        ];
    }
}
