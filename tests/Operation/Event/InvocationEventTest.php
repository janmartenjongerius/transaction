<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Operation\Event;

use Johmanx10\Transaction\Operation\Invocation;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Operation\Event\InvocationEvent;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Operation\Event\InvocationEvent
 */
class InvocationEventTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(
            InvocationEvent::class,
            new InvocationEvent(
                new Invocation(
                    __METHOD__,
                    fn () => null,
                    fn () => null
                )
            )
        );
    }
}
