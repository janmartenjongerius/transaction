<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Operation\Event;

use Johmanx10\Transaction\Operation\Rollback;
use Throwable;

final class RollbackEvent
{
    public function __construct(
        public Rollback $rollback,
        public ?Throwable $reason
    ) {}
}
