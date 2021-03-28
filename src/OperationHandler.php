<?php


namespace Johmanx10\Transaction;

use Johmanx10\Transaction\Exception\OperationExceptionFactory;
use Johmanx10\Transaction\Exception\OperationExceptionFactoryInterface;
use Johmanx10\Transaction\Exception\OperationExceptionInterface;
use Johmanx10\Transaction\Visitor\OperationVisitorAwareInterface;
use Johmanx10\Transaction\Visitor\OperationVisitorInterface;
use Johmanx10\Transaction\Visitor\TransactionFactory;
use Johmanx10\Transaction\Visitor\TransactionFactoryInterface;
use Throwable;

class OperationHandler implements
    OperationHandlerInterface,
    OperationVisitorAwareInterface
{
    /** @var OperationVisitorInterface[] */
    private $visitors = [];

    /** @var TransactionFactoryInterface */
    private $transactionFactory;

    /** @var OperationExceptionFactoryInterface */
    private $exceptionFactory;

    /**
     * Constructor.
     *
     * @param TransactionFactoryInterface|null        $transactionFactory
     * @param OperationExceptionFactoryInterface|null $exceptionFactory
     */
    public function __construct(
        TransactionFactoryInterface $transactionFactory = null,
        OperationExceptionFactoryInterface $exceptionFactory = null
    ) {
        $this->transactionFactory = $transactionFactory ?? new TransactionFactory();
        $this->exceptionFactory   = $exceptionFactory ?? new OperationExceptionFactory();
    }

    /**
     * Handle the given operations.
     *
     * @param OperationInterface ...$operations
     *
     * @return void
     *
     * @throws OperationExceptionInterface When an operation or the transaction
     *   fails.
     */
    public function handle(OperationInterface ...$operations): void
    {
        $transaction = $this
            ->transactionFactory
            ->createTransaction(...$operations);

        try {
            $transaction->commit(...array_values($this->visitors));
        } catch (Throwable $exception) {
            throw $this->exceptionFactory->createFromThrowable($exception);
        }
    }

    /**
     * Attach operation visitors to the current container.
     *
     * @param OperationVisitorInterface ...$visitors
     *
     * @return void
     */
    public function attachVisitor(OperationVisitorInterface ...$visitors): void
    {
        foreach ($visitors as $visitor) {
            $this->visitors[spl_object_hash($visitor)] = $visitor;
        }
    }

    /**
     * Detach operation visitors to the current container.
     *
     * @param OperationVisitorInterface ...$visitors
     *
     * @return void
     */
    public function detachVisitor(OperationVisitorInterface ...$visitors): void
    {
        foreach ($visitors as $visitor) {
            unset($this->visitors[spl_object_hash($visitor)]);
        }
    }
}
