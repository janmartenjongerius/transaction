<?php


namespace Johmanx10\Transaction;

use Johmanx10\Transaction\Exception\TransactionRolledBackExceptionInterface;

interface TransactionInterface
{
    /**
     * Commit the operations in the transaction.
     * Roll back operations in reverse order, from the point where a throwable
     * was caught.
     *
     * @return void
     *
     * @throws TransactionRolledBackExceptionInterface When the transaction was
     *   rolled back.
     */
    public function commit(): void;

    /**
     * Whether the current transaction is committed successfully.
     *
     * @return bool
     */
    public function isCommitted(): bool;
}
