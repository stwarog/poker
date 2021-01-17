<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Event;

use App\Shared\Domain\AbstractDomainEvent;

interface EventBusInterface
{
    public function publish(AbstractDomainEvent ...$events): void;
}
