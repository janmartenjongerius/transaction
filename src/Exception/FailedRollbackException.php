<?php


namespace Johmanx10\Transaction\Exception;

use Johmanx10\Transaction\OperationFailureInterface;
use Johmanx10\Transaction\OperationInterface;
use RuntimeException;
use Throwable;

class FailedRollbackException extends RuntimeException implements FailedRollbackExceptionInterface
{
    /** @var OperationInterface */
    private $operation;

    /** @var OperationFailureInterface[] */
    private $previousRollbacks;

    /**
     * Constructor.
     *
     * @param OperationInterface        $operation
     * @param int                       $code
     * @param Throwable                 $previous
     * @param OperationFailureInterface ...$previousRollbacks
     */
    public function __construct(
        OperationInterface $operation,
        int $code,
        Throwable $previous,
        OperationFailureInterface ...$previousRollbacks
    ) {
        $this->operation         = $operation;
        $this->previousRollbacks = $previousRollbacks;

        parent::__construct(
            sprintf(
                'Failed rolling back operation #%d',
                spl_object_id($operation)
            ),
            $code,
            $previous
        );
    }

    /**
     * Get the operation for which the rollback failed.
     *
     * @return OperationInterface
     */
    public function getOperation(): OperationInterface
    {
        return $this->operation;
    }

    /**
     * Get the rollbacks that succeeded before the current failure.
     *
     * @return OperationFailureInterface[]
     */
    public function getPreviousRollbacks(): array
    {
        return $this->previousRollbacks;
    }
}
