<?php

namespace Johmanx10\Transaction\Formatter;

use Johmanx10\Transaction\DescribableOperationInterface;
use Johmanx10\Transaction\OperationInterface;

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
