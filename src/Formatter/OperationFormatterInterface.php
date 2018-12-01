<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction\Formatter;

use Johmanx10\Transaction\OperationInterface;

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
