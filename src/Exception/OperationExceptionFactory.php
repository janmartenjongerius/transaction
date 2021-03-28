<?php


namespace Johmanx10\Transaction\Exception;

use Johmanx10\Transaction\Formatter\ExceptionFormatter;
use Johmanx10\Transaction\Formatter\ExceptionFormatterInterface;
use Johmanx10\Transaction\Formatter\FailedRollbackFormatter;
use Johmanx10\Transaction\Formatter\FailedRollbackFormatterInterface;
use Johmanx10\Transaction\Formatter\RollbackFormatter;
use Johmanx10\Transaction\Formatter\RollbackFormatterInterface;
use Throwable;

class OperationExceptionFactory implements OperationExceptionFactoryInterface
{
    /** @var RollbackFormatterInterface */
    private $rollbackFormatter;

    /** @var FailedRollbackFormatterInterface */
    private $failedFormatter;

    /** @var ExceptionFormatterInterface */
    private $exceptionFormatter;

    /**
     * Constructor.
     *
     * @param RollbackFormatterInterface|null       $rollbackFormatter
     * @param FailedRollbackFormatterInterface|null $failedFormatter
     * @param ExceptionFormatterInterface|null      $exceptionFormatter
     */
    public function __construct(
        RollbackFormatterInterface $rollbackFormatter = null,
        FailedRollbackFormatterInterface $failedFormatter = null,
        ExceptionFormatterInterface $exceptionFormatter = null
    ) {
        $this->rollbackFormatter  = $rollbackFormatter ?? new RollbackFormatter();
        $this->failedFormatter    = $failedFormatter ?? new FailedRollbackFormatter();
        $this->exceptionFormatter = $exceptionFormatter ?? new ExceptionFormatter();
    }

    /**
     * Create an operation exception from the given throwable.
     *
     * @param Throwable $subject
     *
     * @return OperationExceptionInterface
     */
    public function createFromThrowable(
        Throwable $subject
    ): OperationExceptionInterface {
        if ($subject instanceof TransactionRolledBackExceptionInterface) {
            [$failure] = $subject->getFailures() + [null];

            return new OperationException(
                $this->rollbackFormatter->format($subject),
                $failure
                    ? $failure->getOperation()
                    : null,
                $subject
            );
        }

        if ($subject instanceof FailedRollbackExceptionInterface) {
            return new OperationException(
                $this->failedFormatter->format($subject),
                $subject->getOperation(),
                $subject
            );
        }

        return new OperationException(
            $this->exceptionFormatter->format($subject),
            null,
            $subject
        );
    }
}
