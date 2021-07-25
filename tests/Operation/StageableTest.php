<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Operation;

use Johmanx10\Transaction\Operation\Invocation;
use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Operation\Stage;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Operation\Stageable;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Operation\Stageable
 */
class StageableTest extends TestCase
{
    /**
     * @covers ::stage
     */
    public function testStage(): void
    {
        $subject = new class () implements OperationInterface {
            use Stageable;

            protected function stageOperation(): ?bool
            {
                return null;
            }

            public function __invoke(): Invocation
            {
                return new Invocation($this, fn() => null, fn() => null);
            }

            public function __toString(): string
            {
                return __CLASS__;
            }
        };

        self::assertInstanceOf(Stage::class, $subject->stage());
    }

    /**
     * @covers ::stage
     */
    public function testInvalidStage(): void
    {
        $subject = self::getMockForTrait(Stageable::class);
        self::expectExceptionMessageMatches(
            sprintf(
                '/^Trait \w+ can only be implemented by implementer of %s$/',
                preg_quote(OperationInterface::class)
            )
        );

        if (!method_exists($subject, 'stage')) {
            self::markTestIncomplete(
                sprintf(
                    'Test subject is missing method "stage": %s',
                    get_class($subject)
                )
            );
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $subject->stage();
    }
}
