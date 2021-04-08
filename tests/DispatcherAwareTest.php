<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests;

use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\DispatcherAware;
use Psr\EventDispatcher\EventDispatcherInterface;
use stdClass;

/**
 * @coversDefaultClass \Johmanx10\Transaction\DispatcherAware
 */
class DispatcherAwareTest extends TestCase
{
    /**
     * @dataProvider eventProvider
     *
     * @param object $event
     *
     * @covers ::setDispatcher
     * @covers ::dispatch
     */
    public function testDispatcher(object $event): void
    {
        $subject = new class
        {
            use DispatcherAware;

            public function peekDispatch(object $event): void
            {
                $this->dispatch($event);
            }
        };

        $subject->peekDispatch($event);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $dispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->with($event);

        $subject->setDispatcher($dispatcher);
        $subject->peekDispatch($event);
    }

    public function eventProvider(): array
    {
        return [
            [$this],
            [new stdClass()]
        ];
    }
}
