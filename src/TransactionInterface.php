<?php

declare(strict_types=1);

namespace Johmanx10\Transaction;

use Johmanx10\Transaction\Result\CommitResultInterface;

interface TransactionInterface
{
    public function commit(): CommitResultInterface;
}
