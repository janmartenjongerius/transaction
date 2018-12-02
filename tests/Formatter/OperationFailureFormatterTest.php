<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction\Tests\Formatter;

use Error;
use Exception;
use Johmanx10\Transaction\Formatter\ExceptionFormatterInterface;
use Johmanx10\Transaction\Formatter\OperationFormatterInterface;
use Johmanx10\Transaction\OperationFailureInterface;
use Johmanx10\Transaction\OperationInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Formatter\OperationFailureFormatter;
use Throwable;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Formatter\OperationFailureFormatter
 */
class OperationFailureFormatterTest extends TestCase
{
    private const OPERATION = 'Operation';
    private const EXCEPTION = 'Exception';

    /**
     * @return array
     */
    public function argumentsProvider(): array
    {
        return [
            [null, null],
            [
                $this->createMock(OperationFormatterInterface::class),
                $this->createMock(ExceptionFormatterInterface::class)
            ],
            [
                null,
                $this->createMock(ExceptionFormatterInterface::class)
            ],
            [
                $this->createMock(OperationFormatterInterface::class),
                null
            ]
        ];
    }

    /**
     * @dataProvider argumentsProvider
     *
     * @param OperationFormatterInterface|null $operationFormatter
     * @param ExceptionFormatterInterface|null $exceptionFormatter
     *
     * @return void
     *
     * @covers ::__construct
     */
    public function testConstructor(
        ?OperationFormatterInterface $operationFormatter,
        ?ExceptionFormatterInterface $exceptionFormatter
    ): void {
        $this->assertInstanceOf(
            OperationFailureFormatter::class,
            new OperationFailureFormatter(
                $operationFormatter,
                $exceptionFormatter
            )
        );
    }

    /**
     * @param Throwable|null $exception
     *
     * @return OperationFailureInterface
     */
    private function createFailure(
        ?Throwable $exception
    ): OperationFailureInterface {
        /** @var OperationFailureInterface|MockObject $failure */
        $failure = $this->createMock(OperationFailureInterface::class);

        $failure
            ->expects(self::any())
            ->method('getOperation')
            ->willReturn($this->createMock(OperationInterface::class));

        $failure
            ->expects(self::any())
            ->method('getException')
            ->willReturn($exception);

        return $failure;
    }

    /**
     * @return array
     */
    public function failureProvider(): array
    {
        return [
            [
                $this->createFailure(null),
                sprintf(
                    '/^\(\d+\)\s+✔ %s$/',
                    static::OPERATION
                )
            ],
            [
                $this->createFailure(
                    $this->createMock(Exception::class)
                ),
                sprintf(
                    '/^\(\d+\)\s+∴ %s$/',
                    static::EXCEPTION
                )
            ],
            [
                $this->createFailure(
                    $this->createMock(Error::class)
                ),
                sprintf(
                    '/^\(\d+\)\s+∴ %s$/',
                    static::EXCEPTION
                )
            ]
        ];
    }

    /**
     * @dataProvider failureProvider
     *
     * @param OperationFailureInterface $failure
     * @param string                    $pattern
     *
     * @return void
     *
     * @covers ::format
     */
    public function testFormat(
        OperationFailureInterface $failure,
        string $pattern
    ): void {
        /** @var OperationFormatterInterface|MockObject $operationFormatter */
        $operationFormatter = $this->createMock(OperationFormatterInterface::class);

        /** @var ExceptionFormatterInterface|MockObject $exceptionFormatter */
        $exceptionFormatter = $this->createMock(ExceptionFormatterInterface::class);

        $subject = new OperationFailureFormatter(
            $operationFormatter,
            $exceptionFormatter
        );

        $operationFormatter
            ->expects(self::any())
            ->method('format')
            ->willReturn(static::OPERATION);

        $exceptionFormatter
            ->expects(self::any())
            ->method('format')
            ->willReturn(static::EXCEPTION);

        $this->assertRegExp($pattern, $subject->format($failure));
    }
}
