<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Operation;

use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Result\CommitResultInterface;
use Johmanx10\Transaction\TransactionFactoryInterface;
use Johmanx10\Transaction\TransactionInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Operation\OperationHandler;
use ReflectionProperty;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Operation\OperationHandler
 */
class OperationHandlerTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(
            OperationHandler::class,
            new OperationHandler(
                self::createMock(TransactionFactoryInterface::class)
            )
        );
    }

    /**
     * @covers ::defaultRollback
     * @covers ::withRollback
     */
    public function testCustomRollback(): void
    {
        $subject = new OperationHandler(
            self::createMock(TransactionFactoryInterface::class)
        );
        $property = new ReflectionProperty(
            class: OperationHandler::class,
            property: 'rollback'
        );
        $property->setAccessible(true);

        self::assertNull(
            $property->getValue($subject),
            'Rollback must be initially null'
        );

        $mutated = $subject->withRollback(fn () => null);

        self::assertNull(
            $property->getValue($subject),
            'Rollback must remain null'
        );
        self::assertTrue(
            is_callable($property->getValue($mutated)),
            'Mutated handler must contain a callable rollback'
        );

        $restored = $mutated->defaultRollback();

        self::assertTrue(
            is_callable($property->getValue($mutated)),
            'Mutated rollback must remain callable'
        );
        self::assertNull(
            $property->getValue($restored),
            'Restored rollback must be null'
        );
    }

    /**
     * @dataProvider operationsProvider
     *
     * @covers ::__invoke
     * @covers ::flatten
     *
     * @param int                                                        $expected
     * @param array<OperationInterface>|array<array<OperationInterface>> $operations
     */
    public function testInvoke(
        int $expected,
        array $operations
    ): void {
        $factory = self::createMock(TransactionFactoryInterface::class);
        $subject = new OperationHandler($factory);

        $transaction = self::createMock(TransactionInterface::class);
        $factory
            ->expects(self::once())
            ->method('__invoke')
            ->with(
                ...array_fill(
                    start_index: 0,
                    count: $expected,
                    value: self::isInstanceOf(OperationInterface::class)
                )
            )
            ->willReturn($transaction);

        $result = self::createMock(CommitResultInterface::class);
        $transaction
            ->expects(self::once())
            ->method('commit')
            ->willReturn($result);

        $result
            ->expects(self::once())
            ->method('committed')
            ->willReturn(true);

        $result
            ->expects(self::never())
            ->method('rollback');

        self::assertInstanceOf(
            CommitResultInterface::class,
            $subject(...$operations)
        );
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function operationsProvider(): array
    {
        $operation = self::createMock(OperationInterface::class);

        return [
            'No operations' => [
                'expected' => 0,
                'operations' => []
            ],
            'One flat operation' => [
                'expected' => 1,
                'operations' => [$operation]
            ],
            'One nested operation' => [
                'expected' => 1,
                'operations' => [[$operation]]
            ],
            'Multiple operations' => [
                'expected' => 3,
                'operations' => [$operation, [$operation], $operation]
            ]
        ];
    }

    /**
     * @covers ::__invoke
     */
    public function testRollback(): void
    {
        $factory = self::createMock(TransactionFactoryInterface::class);
        $subject = new OperationHandler($factory);

        $rollBacks = [
            null,
            fn () => true
        ];

        $factory
            ->expects(self::exactly(count($rollBacks)))
            ->method('__invoke')
            ->willReturn(
                ...array_map(
                    function (?callable $rollback): TransactionInterface {
                        /** @var TransactionInterface|MockObject $transaction */
                        $transaction = self::createMock(
                            TransactionInterface::class
                        );

                        $result = self::createMock(
                            CommitResultInterface::class
                        );

                        $transaction
                            ->expects(self::once())
                            ->method('commit')
                            ->willReturn($result);

                        $result
                            ->expects(self::once())
                            ->method('committed')
                            ->willReturn(false);

                        $result
                            ->expects(self::once())
                            ->method('rollback')
                            ->with($rollback);

                        return $transaction;
                    },
                    $rollBacks
                )
            );

        foreach ($rollBacks as $rollBack) {
            $handler = $subject;

            if ($rollBack !== null) {
                $handler = $subject->withRollback($rollBack);
            }

            self::assertInstanceOf(
                CommitResultInterface::class,
                $handler()
            );
        }
    }
}
