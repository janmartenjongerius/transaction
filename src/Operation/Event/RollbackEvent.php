<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Operation\Event;

use Johmanx10\Transaction\Event\DefaultPreventable;
use Johmanx10\Transaction\Event\DefaultPreventableInterface;
use Johmanx10\Transaction\Operation\Describable;
use Johmanx10\Transaction\Operation\Rollback;
use Stringable;
use Throwable;

final class RollbackEvent implements DefaultPreventableInterface, Stringable
{
    use DefaultPreventable;
    use Describable;

    public function __construct(
        public Rollback $rollback,
        public ?Throwable $reason
    ) {
        $this->description = $rollback;
    }
}
