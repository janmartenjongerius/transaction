<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Operation;

use Johmanx10\Transaction\Tests\Descriptor;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Operation\Rollback;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Operation\Rollback
 */
class RollbackTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(
            Rollback::class,
            new Rollback(
                description: __CLASS__,
                rollback: fn () => null
            )
        );

        self::assertInstanceOf(
            Rollback::class,
            new Rollback(
                description: new Descriptor(__CLASS__),
                rollback: fn () => null
            )
        );
    }

    /**
     * @covers ::__invoke
     */
    public function testInvoke(): void
    {
        $hasRun = false;

        $subject = new Rollback(
            description: __CLASS__,
            rollback: function () use (&$hasRun): void {
                $hasRun = true;
            }
        );

        self::assertFalse($hasRun);

        $subject();

        self::assertTrue($hasRun);
    }
}
