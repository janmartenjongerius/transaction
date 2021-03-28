<?php


namespace Johmanx10\Transaction\Visitor;

use Johmanx10\Transaction\Exception\TransactionRolledBackExceptionInterface;
use Johmanx10\Transaction\TransactionInterface;

interface AcceptingTransactionInterface extends TransactionInterface
{
    /**
     * Commit the operations in the transaction.
     * Roll back operations in reverse order, from the point where a throwable
     * was caught.
     *
     * @param OperationVisitorInterface ...$visitors
     *
     * @return void
     *
     * @throws TransactionRolledBackExceptionInterface When the transaction was
     *   rolled back.
     */
    public function commit(OperationVisitorInterface ...$visitors): void;
}
