<?php


namespace Johmanx10\Transaction\Formatter;

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
