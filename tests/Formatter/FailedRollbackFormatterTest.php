<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction\Tests\Formatter;

use Exception;
use Johmanx10\Transaction\Exception\FailedRollbackExceptionInterface;
use Johmanx10\Transaction\Formatter\ExceptionFormatterInterface;
use Johmanx10\Transaction\Formatter\OperationFailureFormatterInterface;
use Johmanx10\Transaction\Formatter\OperationFormatterInterface;
use Johmanx10\Transaction\OperationFailureInterface;
use Johmanx10\Transaction\OperationInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Formatter\FailedRollbackFormatter;
use Throwable;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Formatter\FailedRollbackFormatter
 */
class FailedRollbackFormatterTest extends TestCase
{
    private const OPERATION = 'Operation';
    private const EXCEPTION = 'Exception';
    private const FAILURE   = 'Failure';

    /**
     * @return array
     */
    public function argumentsProvider(): array
    {
        return [
            [null, null, null],
            [
                $this->createMock(OperationFormatterInterface::class),
                $this->createMock(ExceptionFormatterInterface::class),
                $this->createMock(OperationFailureFormatterInterface::class)
            ],
            [
                null,
                null,
                $this->createMock(OperationFailureFormatterInterface::class)
            ],
            [
                $this->createMock(OperationFormatterInterface::class),
                $this->createMock(ExceptionFormatterInterface::class),
                null
            ],
            [
                $this->createMock(OperationFormatterInterface::class),
                null,
                $this->createMock(OperationFailureFormatterInterface::class)
            ],
            [
                null,
                $this->createMock(ExceptionFormatterInterface::class),
                $this->createMock(OperationFailureFormatterInterface::class)
            ]
        ];
    }

    /**
     * @dataProvider argumentsProvider
     *
     * @param OperationFormatterInterface|null        $operationFormatter
     * @param ExceptionFormatterInterface|null        $exceptionFormatter
     * @param OperationFailureFormatterInterface|null $failureFormatter
     *
     * @return void
     *
     * @covers ::__construct
     */
    public function testConstructor(
        ?OperationFormatterInterface $operationFormatter,
        ?ExceptionFormatterInterface $exceptionFormatter,
        ?OperationFailureFormatterInterface $failureFormatter
    ): void {
        $this->assertInstanceOf(
            FailedRollbackFormatter::class,
            new FailedRollbackFormatter(
                $operationFormatter,
                $exceptionFormatter,
                $failureFormatter
            )
        );
    }

    /**
     * @dataProvider rollbackProvider
     *
     * @param FailedRollbackExceptionInterface $rollback
     * @param string                           $expected
     *
     * @return void
     *
     * @covers ::format
     */
    public function testFormat(
        FailedRollbackExceptionInterface $rollback,
        string $expected
    ): void {
        /** @var OperationFormatterInterface|MockObject $operationFormatter */
        $operationFormatter = $this->createMock(OperationFormatterInterface::class);

        /** @var ExceptionFormatterInterface|MockObject $exceptionFormatter */
        $exceptionFormatter = $this->createMock(ExceptionFormatterInterface::class);

        /** @var OperationFailureFormatterInterface|MockObject $failureFormatter */
        $failureFormatter = $this->createMock(OperationFailureFormatterInterface::class);

        $subject = new FailedRollbackFormatter(
            $operationFormatter,
            $exceptionFormatter,
            $failureFormatter
        );

        $operationFormatter
            ->expects(self::any())
            ->method('format')
            ->willReturn(static::OPERATION);

        $exceptionFormatter
            ->expects(self::any())
            ->method('format')
            ->willReturn(static::EXCEPTION);

        $failureFormatter
            ->expects(self::any())
            ->method('format')
            ->willReturn(static::FAILURE);

        $this->assertEquals($expected, $subject->format($rollback));
    }

    /**
     * @param string                    $message
     * @param OperationFailureInterface ...$failures
     *
     * @return FailedRollbackExceptionInterface
     */
    private function createRollback(
        string $message,
        OperationFailureInterface ...$failures
    ): FailedRollbackExceptionInterface {
        /** @var OperationInterface $operation */
        $operation = $this->createMock(OperationInterface::class);

        return new class ($message, $operation, ...$failures) extends Exception implements
            FailedRollbackExceptionInterface
        {
            /** @var OperationInterface */
            private $operation;

            /** @var OperationFailureInterface[] */
            private $previousRollbacks;

            /**
             * Constructor.
             *
             * @param string                    $message
             * @param OperationInterface        $operation
             * @param OperationFailureInterface ...$previousRollbacks
             */
            public function __construct(
                string $message,
                OperationInterface $operation,
                OperationFailureInterface ...$previousRollbacks
            ) {
                $this->operation         = $operation;
                $this->previousRollbacks = $previousRollbacks;
                parent::__construct(
                    $message,
                    0,
                    new Exception('Previous')
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
        };
    }

    /**
     * @return array
     */
    public function rollbackProvider(): array
    {
        $operation = static::OPERATION;
        $exception = static::EXCEPTION;
        $failure   = static::FAILURE;

        return [
            [
                $this->createRollback('Foo'),
                <<<MESSAGE
Foo
$operation
$exception
MESSAGE

            ],
            [
                $this->createRollback(
                    'Bar',
                    $this->createMock(OperationFailureInterface::class)
                ),
                <<<MESSAGE
Bar
$operation
$exception

Previous rollbacks:
$failure
MESSAGE

            ],
            [
                $this->createRollback(
                    'Baz',
                    $this->createMock(OperationFailureInterface::class),
                    $this->createMock(OperationFailureInterface::class),
                    $this->createMock(OperationFailureInterface::class)
                ),
                <<<MESSAGE
Baz
$operation
$exception

Previous rollbacks:
$failure
$failure
$failure
MESSAGE

            ]
        ];
    }
}
