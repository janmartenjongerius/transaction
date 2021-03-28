<?php

namespace Johmanx10\Transaction\Formatter;

use Johmanx10\Transaction\Exception\TransactionRolledBackExceptionInterface;
use Johmanx10\Transaction\OperationFailureInterface;

class RollbackFormatter implements RollbackFormatterInterface
{
    /** @var OperationFailureFormatterInterface */
    private $failureFormatter;

    /**
     * Constructor.
     *
     * @param OperationFailureFormatterInterface|null $failureFormatter
     */
    public function __construct(
        OperationFailureFormatterInterface $failureFormatter = null
    ) {
        $this->failureFormatter = (
            $failureFormatter ?? new OperationFailureFormatter()
        );
    }

    /**
     * Format the given rollback exception into a readable string.
     *
     * @param TransactionRolledBackExceptionInterface $rollback
     *
     * @return string
     */
    public function format(
        TransactionRolledBackExceptionInterface $rollback
    ): string {
        return implode(
            PHP_EOL,
            array_reduce(
                $rollback->getFailures(),
                function (
                    array $carry,
                    OperationFailureInterface $failure
                ): array {
                    $carry[] = $this->failureFormatter->format($failure);

                    return $carry;
                },
                [
                    $rollback->getMessage(),
                    '',
                    'Stacktrace:'
                ]
            )
        );
    }
}
