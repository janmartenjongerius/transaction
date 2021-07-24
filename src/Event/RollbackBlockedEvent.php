<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Event;

final class RollbackBlockedEvent
{
    public function __construct(
        public bool $rolledBack,
        public bool $committed
    ) {
    }
}
