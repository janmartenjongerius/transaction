<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Event;

use Johmanx10\Transaction\Result\StagingResult;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Event\StagingResultEvent;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Event\StagingResultEvent
 */
class StagingResultEventTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(
            StagingResultEvent::class,
            new StagingResultEvent(
                result: new StagingResult()
            )
        );
    }
}
