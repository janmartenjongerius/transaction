<?php


namespace Johmanx10\Transaction\Tests;

use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Operation;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Operation
 */
class OperationTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $this->assertInstanceOf(
            Operation::class,
            new Operation(
                function () {
                }
            )
        );

        $this->assertInstanceOf(
            Operation::class,
            new Operation(
                function () {
                },
                function () {
                }
            )
        );

        $this->assertInstanceOf(
            Operation::class,
            new Operation(
                function () {
                },
                function () {
                },
                'Test operation'
            )
        );
    }

    /**
     * @return void
     *
     * @covers ::__invoke
     */
    public function testInvoke(): void
    {
        $payload = 1;

        $subject = new Operation(
            function () use (&$payload): void {
                $payload++;
            }
        );
        $subject->__invoke();

        $this->assertEquals(2, $payload);
    }

    /**
     * @return void
     *
     * @covers ::rollback
     */
    public function testRollback(): void
    {
        $payload = 1;

        $subject = new Operation(
            function () {
            },
            function () use (&$payload): void {
                $payload++;
            }
        );
        $subject->rollback();

        $this->assertEquals(2, $payload);
    }

    /**
     * @return void
     *
     * @covers ::__toString
     */
    public function testToString(): void
    {
        $subject = new Operation(
            function () {
            },
            null,
            'Foo'
        );

        $this->assertEquals('Foo', $subject->__toString());
    }
}
