<?php


namespace Johmanx10\Transaction\Tests;

use Exception;
use Johmanx10\Transaction\Exception\FailedRollbackException;
use Johmanx10\Transaction\Exception\TransactionRolledBackException;
use Johmanx10\Transaction\OperationInterface;
use Johmanx10\Transaction\Visitor\OperationVisitorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Transaction;
use Throwable;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Transaction
 */
class TransactionTest extends TestCase
{
    /**
     * Create an operation expecting the given number of commits and rollbacks.
     *
     * @param int $numCommits
     * @param int $numRollbacks
     *
     * @return OperationInterface
     */
    private function createOperation(
        int $numCommits = 1,
        int $numRollbacks = 0
    ): OperationInterface {
        /** @var OperationInterface|MockObject $operation */
        $operation = $this->createMock(OperationInterface::class);

        $operation
            ->expects(self::exactly($numCommits))
            ->method('__invoke');

        $operation
            ->expects(self::exactly($numRollbacks))
            ->method('rollback');

        return $operation;
    }

    /**
     * @return array
     */
    public function successfulOperationProvider(): array
    {
        return [
            [],
            [$this->createOperation()],
            [
                $this->createOperation(),
                $this->createOperation(),
                $this->createOperation()
            ]
        ];
    }

    /**
     * @dataProvider successfulOperationProvider
     *
     * @param OperationInterface ...$operations
     *
     * @return void
     *
     * @covers ::__construct
     * @covers ::commit
     * @covers ::isCommitted
     * @covers ::createQueue
     * @covers ::process
     */
    public function testCommit(OperationInterface ...$operations): void
    {
        $subject = new Transaction(...$operations);

        $this->assertInstanceOf(Transaction::class, $subject);
        $this->assertFalse($subject->isCommitted());

        $subject->commit();

        $this->assertTrue($subject->isCommitted());
    }

    /**
     * @return array
     */
    public function rollbackOperationProvider(): array
    {
        /** @var OperationInterface|MockObject $failingOperation */
        $failingOperation = $this->createMock(OperationInterface::class);

        $failingOperation
            ->expects(self::any())
            ->method('__invoke')
            ->willThrowException(
                $this->createMock(Exception::class)
            );

        /** @var OperationInterface|MockObject $failingRollback */
        $failingRollback = $this->createMock(OperationInterface::class);

        $failingRollback
            ->expects(self::any())
            ->method('rollback')
            ->willThrowException(
                $this->createMock(Exception::class)
            );

        return [
            [$failingOperation],
            [
                $this->createOperation(1, 1),
                $failingOperation,
                $this->createOperation(0, 0)
            ],
            [
                $this->createOperation(1, 1),
                $failingOperation,
                $failingRollback
            ]
        ];
    }

    /**
     * @dataProvider rollbackOperationProvider
     *
     * @param OperationInterface ...$operations
     *
     * @return void
     *
     * @covers ::commit
     * @covers ::rollback
     */
    public function testRollback(OperationInterface ...$operations): void
    {
        $subject   = new Transaction(...$operations);
        $exception = null;

        try {
            $subject->commit();
        } catch (Throwable $exception) {
            $this->assertFalse($subject->isCommitted());
        }

        $this->assertInstanceOf(
            TransactionRolledBackException::class,
            $exception
        );
    }

    /**
     * @dataProvider successfulOperationProvider
     *
     * @param OperationInterface ...$operations
     *
     * @return void
     *
     * @covers ::commit
     * @covers ::process
     */
    public function testVisitors(OperationInterface ...$operations): void
    {
        /** @var OperationVisitorInterface|MockObject $visitor */
        $visitor = $this->createMock(OperationVisitorInterface::class);
        $subject = new Transaction(...$operations);

        $visitor
            ->expects(self::exactly(3 * count($operations)))
            ->method('__invoke')
            ->with(self::isInstanceOf(OperationInterface::class));

        $subject->commit($visitor, $visitor, $visitor);

        $this->assertTrue($subject->isCommitted());
    }

    /**
     * @dataProvider rollbackOperationProvider
     *
     * @param OperationInterface ...$operations
     *
     * @return void
     *
     * @covers ::commit
     * @covers ::process
     */
    public function testVisitorsOnFailure(OperationInterface ...$operations): void
    {
        /** @var OperationVisitorInterface|MockObject $visitor */
        $visitor = $this->createMock(OperationVisitorInterface::class);
        $subject = new Transaction(...$operations);

        $visitor
            ->expects(self::atMost(3 * count($operations)))
            ->method('__invoke')
            ->with(self::isInstanceOf(OperationInterface::class));

        try {
            $subject->commit($visitor, $visitor, $visitor);
        } catch (Throwable $exception) {
            $this->assertFalse($subject->isCommitted());
        }
    }

    /**
     * @return array
     */
    public function failingRollbackOperationProvider(): array
    {
        /** @var OperationInterface|MockObject $failingOperation */
        $failingOperation = $this->createMock(OperationInterface::class);

        $failingOperation
            ->expects(self::any())
            ->method('__invoke')
            ->willThrowException(
                $this->createMock(Exception::class)
            );

        /** @var OperationInterface|MockObject $failingRollback */
        $failingRollback = $this->createMock(OperationInterface::class);

        $failingRollback
            ->expects(self::any())
            ->method('rollback')
            ->willThrowException(
                $this->createMock(Exception::class)
            );

        return [
            [1, $failingRollback, $failingOperation],
            [
                2,
                $this->createOperation(1, 0),
                $failingRollback,
                $this->createOperation(1, 1),
                $failingOperation,
                $this->createOperation(0, 0)
            ],
            [
                1,
                $this->createOperation(1, 0),
                $failingRollback,
                $failingOperation
            ]
        ];
    }

    /**
     * @dataProvider failingRollbackOperationProvider
     *
     * @param int                $numPreviousRollbacks
     * @param OperationInterface ...$operations
     *
     * @return void
     *
     * @covers ::commit
     * @covers ::rollback
     */
    public function testFailingRollback(
        int $numPreviousRollbacks,
        OperationInterface ...$operations
    ): void {
        $subject   = new Transaction(...$operations);
        $exception = null;

        try {
            $subject->commit();
        } catch (Throwable $exception) {
            $this->assertFalse($subject->isCommitted());
        }

        $this->assertInstanceOf(
            FailedRollbackException::class,
            $exception
        );

        if ($exception instanceof FailedRollbackException) {
            $this->assertCount(
                $numPreviousRollbacks,
                $exception->getPreviousRollbacks()
            );
        }
    }
}
