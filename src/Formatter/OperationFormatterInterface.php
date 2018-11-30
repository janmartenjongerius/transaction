<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\WarpPipe\Formatter;

use Johmanx10\WarpPipe\OperationInterface;

interface OperationFormatterInterface
{
    /**
     * Format the given operation into a readable string.
     *
     * @param OperationInterface $operation
     *
     * @return string
     */
    public function format(OperationInterface $operation): string;
}
