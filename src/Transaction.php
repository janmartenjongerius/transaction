<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction;

use Johmanx10\Transaction\Exception\FailedRollbackException;
use Johmanx10\Transaction\Exception\TransactionRolledBackException;
use Johmanx10\Transaction\Visitor\AcceptingTransactionInterface;
use Johmanx10\Transaction\Visitor\OperationVisitorInterface;
use SplDoublyLinkedList;
use Throwable;

class Transaction implements AcceptingTransactionInterface
{
    /** @var OperationInterface[] */
    private $operations;

    /** @var bool */
    private $committed = false;

    /**
     * Constructor.
     *
     * @param OperationInterface ...$operations
     */
    public function __construct(OperationInterface ...$operations)
    {
        $this->operations = $operations;
    }

    /**
     * Commit the operations in the transaction.
     * Roll back operations in reverse order, from the point where a throwable
     * was caught.
     *
     * @param OperationVisitorInterface ...$visitors
     *
     * @return void
     *
     * @throws TransactionRolledBackException When the transaction was
     *   rolled back.
     */
    public function commit(OperationVisitorInterface ...$visitors): void
    {
        if ($this->committed === false) {
            $queue = $this->createQueue(...$this->operations);

            try {
                $this->process($queue, ...$visitors);
            } catch (Throwable $exception) {
                throw new TransactionRolledBackException(
                    ...$this->rollback($exception, $queue)
                );
            }
        }

        $this->committed = true;
    }

    /**
     * Whether the current transaction is committed successfully.
     *
     * @return bool
     */
    public function isCommitted(): bool
    {
        return $this->committed;
    }

    /**
     * Rollback the given operations.
     *
     * @param Throwable                                $exception
     * @param SplDoublyLinkedList|OperationInterface[] $queue
     *
     * @return array|OperationFailureInterface[]
     *
     * @throws FailedRollbackException When an operation could not be rolled back.
     */
    private function rollback(
        Throwable $exception,
        SplDoublyLinkedList $queue
    ): array {
        // Reverse the iterator by telling it last in goes first out.
        $queue->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO);

        // Do not rewind the iterator. It has to continue from where it left off.
        // @codingStandardsIgnoreLine
        for ($failures = []; $queue->valid(); $queue->next()) {
            /** @var OperationInterface $operation */
            $operation = $queue->current();

            try {
                $operation->rollback();
            } catch (Throwable $rollbackException) {
                throw new FailedRollbackException(
                    $operation,
                    0,
                    $rollbackException,
                    // @codeCoverageIgnoreStart
                    ...$failures
                    // @codeCoverageIgnoreEnd
                );
            }

            $failures[] = new OperationFailure($operation, $exception);
            $exception  = null;
        }

        return $failures;
    }

    /**
     * Process the queued operations.
     *
     * @param SplDoublyLinkedList|OperationInterface[] $queue
     * @param OperationVisitorInterface                ...$visitors
     *
     * @return void
     */
    private function process(
        SplDoublyLinkedList $queue,
        OperationVisitorInterface ...$visitors
    ): void {
        // Set the iterator to process operations in order.
        // First in goes first out and the queue is kept in-tact while traversing.
        $queue->setIteratorMode(
            SplDoublyLinkedList::IT_MODE_FIFO
            | SplDoublyLinkedList::IT_MODE_KEEP
        );

        // @codingStandardsIgnoreLine
        for ($queue->rewind(); $queue->valid(); $queue->next()) {
            /** @var OperationInterface $operation */
            $operation = $queue->current();

            foreach ($visitors as $visitor) {
                $visitor($operation);
            }

            $operation->__invoke();
        }
    }

    /**
     * Create a queue for the given operations.
     *
     * @param OperationInterface ...$operations
     *
     * @return SplDoublyLinkedList|OperationInterface[]
     */
    private function createQueue(
        OperationInterface ...$operations
    ): SplDoublyLinkedList {
        /** @var SplDoublyLinkedList|OperationInterface[] $queue */
        $queue = new SplDoublyLinkedList();

        foreach ($operations as $operation) {
            $queue->push($operation);
        }

        return $queue;
    }
}
