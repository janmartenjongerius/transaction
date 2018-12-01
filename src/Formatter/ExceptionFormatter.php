<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction\Formatter;

use Throwable;

class ExceptionFormatter implements ExceptionFormatterInterface
{
    /**
     * Format the given exception into a readable string.
     *
     * @param Throwable $exception
     *
     * @return string
     */
    public function format(Throwable $exception): string
    {
        return $exception->getMessage();
    }
}
