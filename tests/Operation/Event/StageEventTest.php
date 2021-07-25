<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Operation\Event;

use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Operation\Stage;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Operation\Event\StageEvent;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Operation\Event\StageEvent
 */
class StageEventTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(
            StageEvent::class,
            new StageEvent(
                new Stage(
                    self::createMock(OperationInterface::class),
                    fn () => null
                )
            )
        );
    }
}
