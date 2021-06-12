<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Operation;

use Johmanx10\Transaction\Result\CommitResult;

interface OperationHandlerInterface
{
    public function __invoke(
        OperationInterface | iterable ...$operations
    ): CommitResult;

    public function withRollback(callable $rollback): static;

    public function defaultRollback(): static;
}