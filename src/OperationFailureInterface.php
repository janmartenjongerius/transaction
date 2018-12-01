<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction;

use Throwable;

interface OperationFailureInterface
{
    /**
     * Get the failed operation.
     *
     * @return OperationInterface
     */
    public function getOperation(): OperationInterface;

    /**
     * Get the exception that caused the operation to fail.
     *
     * @return Throwable|null
     */
    public function getException(): ?Throwable;
}
