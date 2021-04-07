<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Operation;

use Closure;
use Stringable;

final class Operation implements OperationInterface
{
    use Operable;

    public function __construct(
        private Stringable|string $description,
        private Closure $invocation,
        private ?Closure $rollback,
        private ?Closure $stage
    ) {}
}
