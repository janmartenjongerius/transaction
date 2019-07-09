<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Johmanx10\Transaction\Tests;

use Johmanx10\Transaction\Formatter\OperationFormatterInterface;
use Johmanx10\Transaction\OperationInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Visitor\LogOperationVisitor;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Visitor\LogOperationVisitor
 */
class LogOperationVisitorTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $this->assertInstanceOf(
            LogOperationVisitor::class,
            new LogOperationVisitor(
                $this->createMock(LoggerInterface::class)
            )
        );

        $this->assertInstanceOf(
            LogOperationVisitor::class,
            new LogOperationVisitor(
                $this->createMock(LoggerInterface::class),
                $this->createMock(OperationFormatterInterface::class)
            )
        );
    }

    /**
     * @return void
     *
     * @covers ::__invoke
     */
    public function testInvoke(): void
    {
        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->createMock(LoggerInterface::class);

        /** @var OperationFormatterInterface|MockObject $formatter */
        $formatter = $this->createMock(OperationFormatterInterface::class);

        $subject = new LogOperationVisitor($logger, $formatter);

        $logger
            ->expects(self::once())
            ->method('info')
            ->with('Operation foo');

        $formatter
            ->expects(self::once())
            ->method('format')
            ->with(self::isInstanceOf(OperationInterface::class))
            ->willReturn('Operation foo');

        $subject->__invoke($this->createMock(OperationInterface::class));
    }
}
