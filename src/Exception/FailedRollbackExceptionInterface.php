<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\WarpPipe\Exception;

use Johmanx10\WarpPipe\OperationFailureInterface;
use Johmanx10\WarpPipe\OperationInterface;
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
