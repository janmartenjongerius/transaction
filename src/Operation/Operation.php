<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Operation;

use Closure;
use Stringable;

final class Operation implements OperationInterface
{
    use Describable;

    public function __construct(
        private Stringable|string $description,
        private Closure $invocation,
        private ?Closure $rollback,
        private ?Closure $stage
    ) {}

    public function __invoke(): Invocation
    {
        return new Invocation(
            $this,
            $this->invocation,
            $this->rollback ?? fn () => true
        );
    }

    public function stage(): Stage
    {
        return new Stage(
            $this,
            $this->stage ?? fn () => true
        );
    }
}
