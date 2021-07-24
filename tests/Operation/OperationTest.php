<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Operation;

use Johmanx10\Transaction\Operation\Invocation;
use Johmanx10\Transaction\Operation\Stage;
use Johmanx10\Transaction\Tests\Descriptor;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Operation\Operation;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Operation\Operation
 */
class OperationTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(
            Operation::class,
            new Operation(
                description: __CLASS__,
                invocation: fn () => null
            )
        );

        self::assertInstanceOf(
            Operation::class,
            new Operation(
                description: new Descriptor(__CLASS__),
                invocation: fn () => null
            )
        );

        self::assertInstanceOf(
            Operation::class,
            new Operation(
                description: __CLASS__,
                invocation: fn () => null,
                rollback: fn () => true,
                stage: fn () => true
            )
        );
    }

    /**
     * @covers ::stage
     */
    public function testStage(): void
    {
        $subject = new Operation(
            description: __CLASS__,
            invocation: fn () => null,
            stage: fn () => true
        );

        self::assertInstanceOf(Stage::class, $subject->stage());

        $subject = new Operation(
            description: __CLASS__,
            invocation: fn () => null,
            stage: null
        );

        self::assertInstanceOf(Stage::class, $subject->stage());
    }

    /**
     * @covers ::__invoke
     */
    public function testInvoke(): void
    {
        $subject = new Operation(
            description: __CLASS__,
            invocation: fn () => null,
            rollback: fn () => true
        );

        self::assertInstanceOf(Invocation::class, $subject());

        $subject = new Operation(
            description: __CLASS__,
            invocation: fn () => null,
            rollback: null
        );

        self::assertInstanceOf(Invocation::class, $subject());
    }
}
