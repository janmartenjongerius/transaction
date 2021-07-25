<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Operation\Event;

use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Operation\Result\StageResult;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Operation\Event\StageResultEvent;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Operation\Event\StageResultEvent
 */
class StageResultEventTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(
            StageResultEvent::class,
            new StageResultEvent(
                new StageResult(
                    true,
                    true,
                    self::createMock(OperationInterface::class)
                )
            )
        );
    }
}
