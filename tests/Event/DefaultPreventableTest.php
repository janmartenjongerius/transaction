<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Event;

use Johmanx10\Transaction\Event\DefaultPreventableInterface;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Event\DefaultPreventable;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Event\DefaultPreventable
 */
class DefaultPreventableTest extends TestCase
{
    /**
     * @covers ::isDefaultPrevented
     * @covers ::preventDefault
     */
    public function testPreventDefault(): void
    {
        $event = new class implements DefaultPreventableInterface
        {
            use DefaultPreventable;
        };

        self::assertFalse(
            $event->isDefaultPrevented(),
            'Events should initially not be prevented.'
        );

        $event->preventDefault();

        self::assertTrue(
            $event->isDefaultPrevented(),
            'Events should be marked as prevented, after being told so.'
        );
    }
}
