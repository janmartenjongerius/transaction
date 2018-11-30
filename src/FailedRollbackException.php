<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\WarpPipe;

use RuntimeException;
use Throwable;

class FailedRollbackException extends RuntimeException
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
     * @param Throwable|null            $previous
     * @param OperationFailureInterface ...$previousRollbacks
     */
    public function __construct(
        OperationInterface $operation,
        int $code = 0,
        Throwable $previous = null,
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
