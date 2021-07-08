<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Event;

use Johmanx10\Transaction\Operation\Rollback;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Event\RollbackResultEvent;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Event\RollbackResultEvent
 */
class RollbackResultEventTest extends TestCase
{
    /**
     * @dataProvider rollbackProvider
     *
     * @covers ::__construct
     */
    public function testConstruct(Rollback ...$rollbacks): void
    {
        self::assertInstanceOf(
            RollbackResultEvent::class,
            new RollbackResultEvent(...$rollbacks)
        );
    }

    private static function createRollback(): Rollback
    {
        return new Rollback(
            description: __CLASS__,
            rollback: fn () => null
        );
    }

    /**
     * @return array<string,array<Rollback>>
     */
    public function rollbackProvider(): array
    {
        return [
            'No rollbacks' => [],
            'Single rollback' => [self::createRollback()],
            'Many rollbacks' => [
                self::createRollback(),
                self::createRollback(),
                self::createRollback()
            ]
        ];
    }
}
