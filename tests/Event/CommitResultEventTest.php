<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Event;

use Johmanx10\Transaction\Result\CommitResult;
use Johmanx10\Transaction\Result\StagingResult;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Event\CommitResultEvent;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Event\CommitResultEvent
 */
class CommitResultEventTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(
            CommitResultEvent::class,
            new CommitResultEvent(
                result: new CommitResult(
                    new StagingResult()
                )
            )
        );
    }
}
