<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Operation;

use Closure;
use Stringable;

final class Rollback implements Stringable
{
    use Describable;

    public function __construct(
        private Stringable|string $description,
        private Closure $rollback
    ) {}

    /**
     * Perform the rollback.
     */
    public function __invoke(): void
    {
        ($this->rollback)();
    }
}
