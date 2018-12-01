<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction\Formatter;

use Johmanx10\Transaction\Exception\TransactionRolledBackExceptionInterface;

interface RollbackFormatterInterface
{
    /**
     * Format the given rollback exception into a readable string.
     *
     * @param TransactionRolledBackExceptionInterface $rollback
     *
     * @return string
     */
    public function format(
        TransactionRolledBackExceptionInterface $rollback
    ): string;
}
