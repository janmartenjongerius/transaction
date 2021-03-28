<?php


namespace Johmanx10\Transaction;

use Throwable;

class OperationFailure implements OperationFailureInterface
{
    /** @var OperationInterface */
    private $operation;

    /** @var Throwable|null */
    private $exception;

    /**
     * Constructor.
     *
     * @param OperationInterface $operation
     * @param Throwable|null     $exception
     */
    public function __construct(
        OperationInterface $operation,
        ?Throwable $exception
    ) {
        $this->operation = $operation;
        $this->exception = $exception;
    }

    /**
     * Get the failed operation.
     *
     * @return OperationInterface
     */
    public function getOperation(): OperationInterface
    {
        return $this->operation;
    }

    /**
     * Get the exception that caused the operation to fail.
     *
     * @return Throwable|null
     */
    public function getException(): ?Throwable
    {
        return $this->exception;
    }
}
