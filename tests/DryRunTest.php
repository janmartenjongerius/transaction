<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests;

use Johmanx10\Transaction\Operation\Invocation;
use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Operation\Result\InvocationResult;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\DryRun;
use ReflectionMethod;

/**
 * @coversDefaultClass \Johmanx10\Transaction\DryRun
 */
class DryRunTest extends TestCase
{
    /**
     * @dataProvider constructorProvider
     *
     * @param OperationInterface ...$operations
     *
     * @covers ::__construct
     */
    public function testConstruct(OperationInterface ...$operations): void
    {
        $this->assertInstanceOf(
            DryRun::class,
            new DryRun(...$operations)
        );
    }

    public function constructorProvider(): array
    {
        return [
            [],
            [
                $this->createMock(OperationInterface::class)
            ],
            [
                $this->createMock(OperationInterface::class),
                $this->createMock(OperationInterface::class),
                $this->createMock(OperationInterface::class)
            ]
        ];
    }

    /**
     * @covers ::invoke
     */
    public function testInvoke(): void
    {
        $subject = new DryRun();
        $method = new ReflectionMethod($subject, 'invoke');

        $invocation = new Invocation(
            __METHOD__,
            fn () => false,
            fn () => false
        );
        $result = null;

        $method->setAccessible(true);

        try {
            /** @var InvocationResult $result */
            $result = $method->invoke($subject, $invocation);
        } finally {
            $method->setAccessible(false);
        }

        $this->assertInstanceOf(InvocationResult::class, $result);
        $this->assertTrue($result->success);
        $this->assertTrue($result->invoked);
    }
}
