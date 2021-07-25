<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Result;

use Throwable;

interface CommitResultInterface
{
    /**
     * Confirm whether all operations were successful.
     *
     * @return bool
     */
    public function committed(): bool;

    /**
     * Get the exception that caused the transaction not to commit, or null if
     * the transaction succeeded, or the disruption was not caused by an exception.
     *
     * @return Throwable|null
     */
    public function getReason(): ?Throwable;

    /**
     * Roll back the operations that weren't skipped.
     *
     * @param callable|null $rollback
     */
    public function rollback(callable $rollback = null): void;
}
