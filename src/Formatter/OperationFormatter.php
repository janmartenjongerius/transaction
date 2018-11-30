<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\WarpPipe\Formatter;

use Johmanx10\WarpPipe\DescribableOperationInterface;
use Johmanx10\WarpPipe\OperationInterface;

class OperationFormatter implements OperationFormatterInterface
{
    /**
     * Format the given operation into a readable string.
     *
     * @param OperationInterface $operation
     *
     * @return string
     */
    public function format(OperationInterface $operation): string
    {
        return $operation instanceof DescribableOperationInterface
            ? (string)$operation
            : sprintf(
                'Generic operation %s',
                spl_object_hash($operation)
            );
    }
}
