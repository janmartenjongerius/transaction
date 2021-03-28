<?php


namespace Johmanx10\Transaction\Tests\Exception;

use Johmanx10\Transaction\Exception\FailedRollbackExceptionInterface;
use Johmanx10\Transaction\Exception\OperationExceptionInterface;
use Johmanx10\Transaction\Exception\TransactionRolledBackExceptionInterface;
use Johmanx10\Transaction\Formatter\ExceptionFormatterInterface;
use Johmanx10\Transaction\Formatter\FailedRollbackFormatterInterface;
use Johmanx10\Transaction\Formatter\RollbackFormatterInterface;
use Johmanx10\Transaction\OperationFailureInterface;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Exception\OperationExceptionFactory;
use RuntimeException;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Exception\OperationExceptionFactory
 */
class OperationExceptionFactoryTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $this->assertInstanceOf(
            OperationExceptionFactory::class,
            new OperationExceptionFactory()
        );

        $this->assertInstanceOf(
            OperationExceptionFactory::class,
            new OperationExceptionFactory(
                $this->createMock(RollbackFormatterInterface::class)
            )
        );

        $this->assertInstanceOf(
            OperationExceptionFactory::class,
            new OperationExceptionFactory(
                $this->createMock(RollbackFormatterInterface::class),
                $this->createMock(FailedRollbackFormatterInterface::class)
            )
        );

        $this->assertInstanceOf(
            OperationExceptionFactory::class,
            new OperationExceptionFactory(
                $this->createMock(RollbackFormatterInterface::class),
                $this->createMock(FailedRollbackFormatterInterface::class),
                $this->createMock(ExceptionFormatterInterface::class)
            )
        );
    }

    /**
     * @return void
     *
     * @covers ::createFromThrowable
     */
    public function testCreateFromRollback(): void
    {
        $formatter = $this->createMock(RollbackFormatterInterface::class);

        $subject = new OperationExceptionFactory($formatter);

        $this->assertInstanceOf(
            OperationExceptionInterface::class,
            $subject->createFromThrowable(
                $this->createMock(TransactionRolledBackExceptionInterface::class)
            )
        );

        $exception = $this->createMock(TransactionRolledBackExceptionInterface::class);

        $exception
            ->expects(self::once())
            ->method('getFailures')
            ->willReturn(
                [
                    $this->createMock(OperationFailureInterface::class),
                    $this->createMock(OperationFailureInterface::class),
                    $this->createMock(OperationFailureInterface::class)
                ]
            );

        $this->assertInstanceOf(
            OperationExceptionInterface::class,
            $subject->createFromThrowable($exception)
        );
    }

    /**
     * @return void
     *
     * @covers ::createFromThrowable
     */
    public function testCreateFromFailedRollback(): void
    {
        $formatter = $this->createMock(FailedRollbackFormatterInterface::class);

        $subject = new OperationExceptionFactory(null, $formatter);

        $this->assertInstanceOf(
            OperationExceptionInterface::class,
            $subject->createFromThrowable(
                $this->createMock(FailedRollbackExceptionInterface::class)
            )
        );
    }

    /**
     * @return void
     *
     * @covers ::createFromThrowable
     */
    public function testCreateFromGenericException(): void
    {
        $formatter = $this->createMock(ExceptionFormatterInterface::class);

        $subject = new OperationExceptionFactory(null, null, $formatter);

        $this->assertInstanceOf(
            OperationExceptionInterface::class,
            $subject->createFromThrowable(
                $this->createMock(RuntimeException::class)
            )
        );
    }
}
