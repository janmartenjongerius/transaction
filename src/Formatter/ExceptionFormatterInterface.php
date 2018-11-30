<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\WarpPipe\Formatter;

use Throwable;

interface ExceptionFormatterInterface
{
    /**
     * Format the given exception into a readable string.
     *
     * @param Throwable $exception
     *
     * @return string
     */
    public function format(Throwable $exception): string;
}
