<?php


namespace Johmanx10\Transaction\Tests\Exception;

use Johmanx10\Transaction\OperationFailureInterface;
use Johmanx10\Transaction\OperationInterface;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Exception\FailedRollbackException;
use Throwable;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Exception\FailedRollbackException
 */
class FailedRollbackExceptionTest extends TestCase
{
    /**
     * @return array
     */
    public function failureProvider(): array
    {
        return [
            [],
            [
                $this->createMock(OperationFailureInterface::class)
            ],
            [
                $this->createMock(OperationFailureInterface::class),
                $this->createMock(OperationFailureInterface::class),
                $this->createMock(OperationFailureInterface::class)
            ]
        ];
    }

    /**
     * @dataProvider failureProvider
     *
     * @param OperationFailureInterface ...$previousRollbacks
     *
     * @return void
     *
     * @covers ::__construct
     * @covers ::getOperation
     * @covers ::getPreviousRollbacks
     */
    public function testException(
        OperationFailureInterface ...$previousRollbacks
    ): void {
        $subject = new FailedRollbackException(
            $this->createMock(OperationInterface::class),
            0,
            $this->createMock(Throwable::class),
            ...$previousRollbacks
        );

        $this->assertInstanceOf(FailedRollbackException::class, $subject);
        $this->assertInstanceOf(
            OperationInterface::class,
            $subject->getOperation()
        );
        $this->assertEquals(
            $previousRollbacks,
            $subject->getPreviousRollbacks()
        );
        $this->assertStringMatchesFormat(
            'Failed rolling back operation #%d',
            $subject->getMessage()
        );
    }
}
