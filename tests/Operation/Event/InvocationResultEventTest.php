<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Operation\Event;

use Johmanx10\Transaction\Operation\Result\InvocationResult;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Operation\Event\InvocationResultEvent;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Operation\Event\InvocationResultEvent
 */
class InvocationResultEventTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(
            InvocationResultEvent::class,
            new InvocationResultEvent(
                new InvocationResult(
                    __METHOD__,
                    true,
                    true,
                    null,
                    fn () => null
                )
            )
        );
    }
}
