<?php

namespace Johmanx10\Transaction\Formatter;

use Johmanx10\Transaction\Exception\FailedRollbackExceptionInterface;
use Johmanx10\Transaction\OperationFailureInterface;

class FailedRollbackFormatter implements FailedRollbackFormatterInterface
{
    /** @var OperationFormatterInterface */
    private $operationFormatter;

    /** @var ExceptionFormatterInterface */
    private $exceptionFormatter;

    /** @var OperationFailureFormatterInterface */
    private $failureFormatter;

    /**
     * Constructor.
     *
     * @param OperationFormatterInterface|null        $operationFormatter
     * @param ExceptionFormatterInterface|null        $exceptionFormatter
     * @param OperationFailureFormatterInterface|null $failureFormatter
     */
    public function __construct(
        OperationFormatterInterface $operationFormatter = null,
        ExceptionFormatterInterface $exceptionFormatter = null,
        OperationFailureFormatterInterface $failureFormatter = null
    ) {
        $this->operationFormatter = (
            $operationFormatter ?? new OperationFormatter()
        );
        $this->exceptionFormatter = (
            $exceptionFormatter ?? new ExceptionFormatter()
        );
        $this->failureFormatter   = (
            $failureFormatter ?? new OperationFailureFormatter(
                $this->operationFormatter,
                $this->exceptionFormatter
            )
        );
    }

    /**
     * Format the failed rollback to a readable string.
     *
     * @param FailedRollbackExceptionInterface $rollback
     *
     * @return string
     */
    public function format(FailedRollbackExceptionInterface $rollback): string
    {
        $previous = $rollback->getPreviousRollbacks();
        $header   = [
            $rollback->getMessage(),
            $this->operationFormatter->format($rollback->getOperation()),
            $this->exceptionFormatter->format($rollback->getPrevious()),
        ];

        if (!empty($previous)) {
            $header[] = '';
            $header[] = 'Previous rollbacks:';
        }

        return implode(
            PHP_EOL,
            array_reduce(
                $previous,
                function (
                    array $carry,
                    OperationFailureInterface $failure
                ): array {
                    $carry[] = $this->failureFormatter->format($failure);

                    return $carry;
                },
                $header
            )
        );
    }
}
