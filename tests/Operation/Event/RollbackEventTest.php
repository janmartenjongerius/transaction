<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Operation\Event;

use Johmanx10\Transaction\Operation\Rollback;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Operation\Event\RollbackEvent;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Operation\Event\RollbackEvent
 */
class RollbackEventTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(
            RollbackEvent::class,
            new RollbackEvent(
                new Rollback(__METHOD__, fn () => null),
                null
            )
        );
    }
}
