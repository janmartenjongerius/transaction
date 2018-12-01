<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction\Formatter;

use Johmanx10\Transaction\OperationFailureInterface;

interface OperationFailureFormatterInterface
{
    /**
     * Format the operation failure into a readable string.
     *
     * @param OperationFailureInterface $failure
     *
     * @return string
     */
    public function format(OperationFailureInterface $failure): string;
}
