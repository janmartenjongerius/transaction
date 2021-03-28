<?php

namespace Johmanx10\Transaction\Exception;

use Johmanx10\Transaction\OperationFailureInterface;
use Throwable;

interface TransactionRolledBackExceptionInterface extends Throwable
{
    /**
     * Get the failed operations.
     *
     * @return OperationFailureInterface[]
     */
    public function getFailures(): array;
}
