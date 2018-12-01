<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction\Exception;

use Johmanx10\Transaction\OperationFailureInterface;
use RuntimeException;

class TransactionRolledBackException extends RuntimeException implements TransactionRolledBackExceptionInterface
{
    /** @var OperationFailureInterface[] */
    private $failures;

    /**
     * Constructor.
     *
     * @param OperationFailureInterface ...$failures
     */
    public function __construct(OperationFailureInterface ...$failures)
    {
        $this->failures = $failures;

        parent::__construct(
            sprintf(
                '%d operations were rolled back: %s',
                count($failures),
                implode(
                    ', ',
                    array_map(
                        function (OperationFailureInterface $failure): int {
                            return spl_object_id($failure->getOperation());
                        },
                        $failures
                    )
                )
            )
        );
    }

    /**
     * Get the failed operations.
     *
     * @return OperationFailureInterface[]
     */
    public function getFailures(): array
    {
        return $this->failures;
    }
}
