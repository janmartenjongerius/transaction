<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\WarpPipe\Exception;

use Johmanx10\WarpPipe\OperationFailureInterface;
use Throwable;

interface OperationRolledBackExceptionInterface extends Throwable
{
    /**
     * Get the failed operations.
     *
     * @return OperationFailureInterface[]
     */
    public function getFailures(): array;
}
