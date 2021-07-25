<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Operation;

use Closure;
use Johmanx10\Transaction\Operation\Result\InvocationResult;
use Johmanx10\Transaction\Tests\Descriptor;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Operation\Invocation;
use RuntimeException;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Operation\Invocation
 */
class InvocationTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(
            Invocation::class,
            new Invocation(
                description: __CLASS__,
                operation: fn () => null,
                rollback: fn () => null
            )
        );

        self::assertInstanceOf(
            Invocation::class,
            new Invocation(
                description: new Descriptor(__CLASS__),
                operation: fn () => null,
                rollback: fn () => null
            )
        );
    }

    /**
     * @dataProvider operationProvider
     *
     * @covers ::__invoke
     *
     * @param Closure $operation
     * @param bool    $expected
     */
    public function testInvoke(Closure $operation, bool $expected): void
    {
        $subject = new Invocation(
            description: __METHOD__,
            operation: $operation,
            rollback: fn () => null
        );

        $result = $subject->__invoke();
        self::assertInstanceOf(InvocationResult::class, $result);
        self::assertTrue($result->invoked);
        self::assertEquals($expected, $result->success);
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function operationProvider(): array
    {
        return [
            'Success, bool' => [
                'operation' => fn () => true,
                'expected' => true
            ],
            'Success, any' => [
                'operation' => fn () => null,
                'expected' => true
            ],
            'Failure, bool' => [
                'operation' => fn () => false,
                'expected' => false
            ],
            'Failure, exception' => [
                'operation' => fn () => throw new RuntimeException('Break'),
                'expected' => false
            ]
        ];
    }
}
