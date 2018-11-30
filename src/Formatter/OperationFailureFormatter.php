<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\WarpPipe\Formatter;

use Johmanx10\WarpPipe\OperationFailureInterface;

class OperationFailureFormatter implements OperationFailureFormatterInterface
{
    /** @var OperationFormatterInterface */
    private $operationFormatter;

    /** @var ExceptionFormatterInterface */
    private $exceptionFormatter;

    /**
     * Constructor.
     *
     * @param OperationFormatterInterface|null $operationFormatter
     * @param ExceptionFormatterInterface|null $exceptionFormatter
     */
    public function __construct(
        OperationFormatterInterface $operationFormatter = null,
        ExceptionFormatterInterface $exceptionFormatter = null
    ) {
        $this->operationFormatter = (
            $operationFormatter ?? new OperationFormatter()
        );
        $this->exceptionFormatter = (
            $exceptionFormatter ?? new ExceptionFormatter()
        );
    }

    /**
     * Format the operation failure into a readable string.
     *
     * @param OperationFailureInterface $failure
     *
     * @return string
     */
    public function format(OperationFailureInterface $failure): string
    {
        $exception = $failure->getException();

        return $exception !== null
            ? sprintf(
                '❌ %s',
                $this->exceptionFormatter->format($exception)
            )
            : sprintf(
                '✔ %s',
                $this->operationFormatter->format($failure->getOperation())
            );
    }
}
