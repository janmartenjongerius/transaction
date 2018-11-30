<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\WarpPipe\Formatter;

use Johmanx10\WarpPipe\OperationFailureInterface;
use Johmanx10\WarpPipe\OperationRolledBackException;

class RollbackFormatter implements RollbackFormatterInterface
{
    private const OPERATION_PADDING_LENGTH    = 8;
    private const OPERATION_PADDING_STRING    = ' ';
    private const OPERATION_PADDING_DIRECTION = STR_PAD_LEFT;

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
     * @param OperationRolledBackException $rollback
     *
     * @return string
     */
    public function format(OperationRolledBackException $rollback): string
    {
        return implode(
            PHP_EOL,
            array_reduce(
                $rollback->getFailures(),
                function (
                    array $carry,
                    OperationFailureInterface $failure
                ): array {
                    $carry[] = str_pad(
                        (string)spl_object_id($failure->getOperation()),
                        static::OPERATION_PADDING_LENGTH,
                        static::OPERATION_PADDING_STRING,
                        static::OPERATION_PADDING_DIRECTION
                    )
                        . sprintf(
                            ': %s',
                            $this->failureFormatter->format($failure)
                        );

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
