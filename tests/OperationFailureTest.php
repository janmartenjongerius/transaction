<?php


namespace Johmanx10\Transaction\Tests;

use Exception;
use Johmanx10\Transaction\OperationInterface;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\OperationFailure;
use Throwable;

/**
 * @coversDefaultClass \Johmanx10\Transaction\OperationFailure
 */
class OperationFailureTest extends TestCase
{
    /**
     * @return array
     */
    public function argumentsProvider(): array
    {
        return [
            [
                $this->createMock(OperationInterface::class),
                $this->createMock(Exception::class)
            ],
            [
                $this->createMock(OperationInterface::class),
                null
            ]
        ];
    }

    /**
     * @dataProvider argumentsProvider
     *
     * @param OperationInterface $operation
     * @param Throwable|null     $exception
     *
     * @return void
     *
     * @covers ::__construct
     */
    public function testConstructor(
        OperationInterface $operation,
        ?Throwable $exception
    ): void {
        $this->assertInstanceOf(
            OperationFailure::class,
            new OperationFailure($operation, $exception)
        );
    }

    /**
     * @dataProvider argumentsProvider
     *
     * @param OperationInterface $operation
     * @param Throwable|null     $exception
     *
     * @return void
     *
     * @covers ::getOperation
     */
    public function testGetOperation(
        OperationInterface $operation,
        ?Throwable $exception
    ): void {
        $subject = new OperationFailure($operation, $exception);

        $this->assertInstanceOf(
            OperationInterface::class,
            $subject->getOperation()
        );
    }

    /**
     * @dataProvider argumentsProvider
     *
     * @param OperationInterface $operation
     * @param Throwable|null     $exception
     *
     * @return void
     *
     * @covers ::getException
     */
    public function testGetException(
        OperationInterface $operation,
        ?Throwable $exception
    ): void {
        $subject = new OperationFailure($operation, $exception);

        $this->assertEquals($exception, $subject->getException());
    }
}
