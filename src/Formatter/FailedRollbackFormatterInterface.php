<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction\Formatter;

use Johmanx10\Transaction\Exception\FailedRollbackExceptionInterface;

interface FailedRollbackFormatterInterface
{
    /**
     * Format the failed rollback to a readable string.
     *
     * @param FailedRollbackExceptionInterface $rollback
     *
     * @return string
     */
    public function format(FailedRollbackExceptionInterface $rollback): string;
}
