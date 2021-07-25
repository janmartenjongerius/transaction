<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Operation\Result;

use Johmanx10\Transaction\Operation\Rollback;
use Johmanx10\Transaction\Tests\Descriptor;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Operation\Result\InvocationResult;
use RuntimeException;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Operation\Result\InvocationResult
 */
class InvocationResultTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(
            InvocationResult::class,
            new InvocationResult(
                description: __METHOD__,
                success: true,
                invoked: true,
                exception: null,
                rollback: fn () => null
            )
        );

        self::assertInstanceOf(
            InvocationResult::class,
            new InvocationResult(
                description: new Descriptor(__METHOD__),
                success: true,
                invoked: true,
                exception: new RuntimeException(__METHOD__),
                rollback: fn () => null
            )
        );
    }

    /**
     * @covers ::rollback
     */
    public function testRollback(): void
    {
        $subject = new InvocationResult(
            description: new Descriptor(__METHOD__),
            success: true,
            invoked: true,
            exception: new RuntimeException(__METHOD__),
            rollback: fn () => null
        );

        self::assertInstanceOf(Rollback::class, $subject->rollback());
    }
}
