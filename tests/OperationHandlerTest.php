<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction\Tests;

use Johmanx10\Transaction\OperationInterface;
use Johmanx10\Transaction\TransactionFactoryInterface;
use Johmanx10\Transaction\TransactionInterface;
use Johmanx10\Transaction\Visitor\OperationVisitorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\OperationHandler;

/**
 * @coversDefaultClass \Johmanx10\Transaction\OperationHandler
 */
class OperationHandlerTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $this->assertInstanceOf(
            OperationHandler::class,
            new OperationHandler()
        );

        $this->assertInstanceOf(
            OperationHandler::class,
            new OperationHandler(
                $this->createMock(TransactionFactoryInterface::class)
            )
        );
    }

    /**
     * @return void
     *
     * @covers ::handle
     * @covers ::attachVisitor
     * @covers ::detachVisitor
     */
    public function testHandler(): void
    {
        $visitorA = $this->createMock(OperationVisitorInterface::class);
        $visitorB = $this->createMock(OperationVisitorInterface::class);

        /** @var TransactionFactoryInterface|MockObject $factory */
        $factory = $this->createMock(TransactionFactoryInterface::class);

        $subject = new OperationHandler($factory);
        $subject->attachVisitor($visitorA, $visitorB);

        $transactionA = $this->createMock(TransactionInterface::class);
        $transactionB = $this->createMock(TransactionInterface::class);

        $factory
            ->expects(self::exactly(2))
            ->method('createTransaction')
            ->willReturn(
                $transactionA,
                $transactionB
            );

        $operations = [
            $this->createMock(OperationInterface::class),
            $this->createMock(OperationInterface::class),
            $this->createMock(OperationInterface::class)
        ];

        $transactionA
            ->expects(self::once())
            ->method('commit')
            ->with($visitorA, $visitorB);

        $subject->handle(...$operations);

        $transactionB
            ->expects(self::once())
            ->method('commit')
            ->with($visitorB);

        $subject->detachVisitor($visitorA);
        $subject->handle(...$operations);
    }
}
