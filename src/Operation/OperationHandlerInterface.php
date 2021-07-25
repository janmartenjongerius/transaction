<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Operation;

use Johmanx10\Transaction\Result\CommitResultInterface;

interface OperationHandlerInterface
{
    /**
     * @param OperationInterface[]|OperationInterface ...$operations
     *
     * @return CommitResultInterface
     */
    public function __invoke(
        OperationInterface | iterable ...$operations
    ): CommitResultInterface;

    public function withRollback(callable $rollback): static;

    public function defaultRollback(): static;
}
