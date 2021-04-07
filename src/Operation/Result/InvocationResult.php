<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Operation\Result;

use Closure;
use Johmanx10\Transaction\Operation\Describable;
use Johmanx10\Transaction\Operation\Rollback;
use Stringable;
use Throwable;

final class InvocationResult implements Stringable
{
    use Describable;

    public function __construct(
        private Stringable|string $description,
        public bool $success,
        public bool $invoked,
        public ?Throwable $exception,
        private Closure $rollback
    ) {}

    public function rollback(): Rollback
    {
        return new Rollback($this, $this->rollback);
    }
}
