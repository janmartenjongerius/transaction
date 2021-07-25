<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Operation;

use Johmanx10\Transaction\Operation\Invocation;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Operation\Invokable;
use Stringable;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Operation\Invokable
 */
class InvokableTest extends TestCase
{
    /**
     * @covers ::__invoke
     */
    public function testInvoke(): void
    {
        $subject = new class () implements Stringable {
            use Invokable;

            protected function run(): ?bool
            {
                return true;
            }

            protected function rollback(): void
            {
                // no-op
            }

            public function __toString(): string
            {
                return __CLASS__;
            }
        };

        self::assertInstanceOf(Invocation::class, $subject->__invoke());
    }
}
