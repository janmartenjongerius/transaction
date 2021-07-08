<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Event;

use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Event\RollbackBlockedEvent;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Event\RollbackBlockedEvent
 */
class RollbackBlockedEventTest extends TestCase
{
    /**
     * @dataProvider argumentsProvider
     *
     * @param bool $rolledBack
     * @param bool $committed
     *
     * @covers ::__construct
     */
    public function testConstruct(bool $rolledBack, bool $committed): void
    {
        self::assertInstanceOf(
            RollbackBlockedEvent::class,
            new RollbackBlockedEvent(
                rolledBack: $rolledBack,
                committed: $committed
            )
        );
    }

    /**
     * @return array<string,array<string,bool>>
     */
    public function argumentsProvider(): array
    {
        return [
            'Neither committed, nor rolled back' => [
                'rolledBack' => false,
                'committed' => false
            ],
            'Committed, nor rolled back' => [
                'rolledBack' => false,
                'committed' => true
            ],
            'Not committed, rolled back' => [
                'rolledBack' => true,
                'committed' => false
            ],
            'Both committed and rolled back' => [
                'rolledBack' => true,
                'committed' => true
            ]
        ];
    }
}
