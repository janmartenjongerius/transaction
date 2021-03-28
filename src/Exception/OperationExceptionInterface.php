<?php


namespace Johmanx10\Transaction\Exception;

use Johmanx10\Transaction\OperationInterface;
use Throwable;

interface OperationExceptionInterface extends Throwable
{
    /**
     * Get the operation that caused the exception to occur.
     * Returns null if the operation cannot be determined.
     *
     * @return OperationInterface|null
     */
    public function getOperation(): ?OperationInterface;
}
