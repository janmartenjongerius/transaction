<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\WarpPipe\Formatter;

use Johmanx10\WarpPipe\FailedRollbackException;

interface FailedRollbackFormatterInterface
{
    /**
     * Format the failed rollback to a readable string.
     *
     * @param FailedRollbackException $rollback
     *
     * @return string
     */
    public function format(FailedRollbackException $rollback): string;
}
