<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\WarpPipe\Formatter;

use Johmanx10\WarpPipe\Exception\OperationRolledBackExceptionInterface;

interface RollbackFormatterInterface
{
    /**
     * Format the given rollback exception into a readable string.
     *
     * @param OperationRolledBackExceptionInterface $rollback
     *
     * @return string
     */
    public function format(OperationRolledBackExceptionInterface $rollback): string;
}
