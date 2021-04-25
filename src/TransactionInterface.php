<?php

declare(strict_types=1);

namespace Johmanx10\Transaction;

use Johmanx10\Transaction\Result\CommitResult;

interface TransactionInterface
{
    public function commit(callable $rollback = null): CommitResult;
}
