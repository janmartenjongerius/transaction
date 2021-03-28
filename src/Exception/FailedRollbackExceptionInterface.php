<?php


namespace Johmanx10\Transaction\Exception;

use Johmanx10\Transaction\OperationFailureInterface;
use Johmanx10\Transaction\OperationInterface;
use Throwable;

interface FailedRollbackExceptionInterface extends Throwable
{
    /**
     * Get the operation for which the rollback failed.
     *
     * @return OperationInterface
     */
    public function getOperation(): OperationInterface;

    /**
     * Get the rollbacks that succeeded before the current failure.
     *
     * @return OperationFailureInterface[]
     */
    public function getPreviousRollbacks(): array;
}
