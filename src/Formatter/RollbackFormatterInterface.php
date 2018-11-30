<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\WarpPipe\Formatter;

use Johmanx10\WarpPipe\OperationRolledBackException;

interface RollbackFormatterInterface
{
    /**
     * Format the given rollback exception into a readable string.
     *
     * @param OperationRolledBackException $rollback
     *
     * @return string
     */
    public function format(OperationRolledBackException $rollback): string;
}
