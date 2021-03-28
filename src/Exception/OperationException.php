<?php


namespace Johmanx10\Transaction\Exception;

use Johmanx10\Transaction\OperationInterface;
use RuntimeException;
use Throwable;

class OperationException extends RuntimeException implements
    OperationExceptionInterface
{
    /** @var OperationInterface|null */
    private $operation;

    /**
     * Constructor.
     *
     * @param string                  $message
     * @param OperationInterface|null $operation
     * @param Throwable|null          $previous
     */
    public function __construct(
        string $message,
        ?OperationInterface $operation,
        Throwable $previous = null
    ) {
        $this->operation = $operation;
        parent::__construct($message, 0, $previous);
    }

    /**
     * Get the operation that caused the exception to occur.
     * Returns null if the operation cannot be determined.
     *
     * @return OperationInterface|null
     */
    public function getOperation(): ?OperationInterface
    {
        return $this->operation;
    }
}
