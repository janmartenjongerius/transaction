<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Result;

use Johmanx10\Transaction\Event\RollbackBlockedEvent;
use Johmanx10\Transaction\Event\RollbackResultEvent;
use Johmanx10\Transaction\Operation\Event\RollbackEvent;
use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Operation\Result\InvocationResult;
use Johmanx10\Transaction\Operation\Result\StageResult;
use Johmanx10\Transaction\Result\StagingResult;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Result\CommitResult;
use Psr\EventDispatcher\EventDispatcherInterface;
use RuntimeException;
use Throwable;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Result\CommitResult
 */
class CommitResultTest extends TestCase
{
    /**
     * @dataProvider argumentsProvider
     *
     * @covers ::__construct
     *
     * @param StagingResult    $staging
     * @param InvocationResult ...$results
     */
    public function testConstruct(
        StagingResult $staging,
        InvocationResult ...$results
    ): void {
        self::assertInstanceOf(
            CommitResult::class,
            new CommitResult($staging, ...$results)
        );
    }

    /**
     * @return array<string,array<mixed>>
     */
    public function argumentsProvider(): array
    {
        return [
            'No invocations' => [
                new StagingResult()
            ],
            'One invocation' => [
                new StagingResult(),
                new InvocationResult(
                    description: __METHOD__,
                    success: true,
                    invoked: true,
                    exception: null,
                    rollback: fn() => null
                )
            ],
            'Many invocations' => [
                new StagingResult(),
                new InvocationResult(
                    description: __METHOD__,
                    success: true,
                    invoked: true,
                    exception: null,
                    rollback: fn() => null
                ),
                new InvocationResult(
                    description: __METHOD__,
                    success: true,
                    invoked: true,
                    exception: null,
                    rollback: fn() => null
                ),
                new InvocationResult(
                    description: __METHOD__,
                    success: true,
                    invoked: true,
                    exception: null,
                    rollback: fn() => null
                )
            ]
        ];
    }

    /**
     * @dataProvider committedProvider
     *
     * @covers ::committed
     *
     * @param StagingResult      $staging
     * @param InvocationResult[] $results
     * @param bool               $expected
     */
    public function testCommitted(
        StagingResult $staging,
        iterable $results,
        bool $expected
    ): void {
        $subject = new CommitResult($staging, ...$results);
        self::assertEquals($expected, $subject->committed());
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function committedProvider(): array
    {
        return [
            'Staged, Empty' => [
                'staging' => new StagingResult(),
                'results' => [],
                'expected' => true,
            ],
            'Staged, Success' => [
                'staging' => new StagingResult(),
                'results' => [
                    new InvocationResult(
                        description: __METHOD__,
                        success: true,
                        invoked: true,
                        exception: null,
                        rollback: fn() => null
                    )
                ],
                'expected' => true
            ],
            'Staged, Failed all' => [
                'staging' => new StagingResult(),
                'results' => [
                    new InvocationResult(
                        description: __METHOD__,
                        success: false,
                        invoked: true,
                        exception: null,
                        rollback: fn() => null
                    )
                ],
                'expected' => false
            ],
            'Staged, Failed one' => [
                'staging' => new StagingResult(),
                'results' => [
                    // Success
                    new InvocationResult(
                        description: __METHOD__,
                        success: true,
                        invoked: true,
                        exception: null,
                        rollback: fn() => null
                    ),
                    // Failed
                    new InvocationResult(
                        description: __METHOD__,
                        success: false,
                        invoked: true,
                        exception: null,
                        rollback: fn() => null
                    ),
                    // Success
                    new InvocationResult(
                        description: __METHOD__,
                        success: true,
                        invoked: true,
                        exception: null,
                        rollback: fn() => null
                    )
                ],
                'expected' => false
            ]
        ];
    }

    /**
     * @dataProvider reasonProvider
     *
     * @covers ::getReason
     *
     * @param InvocationResult[]   $results
     * @param Throwable|null $expected
     */
    public function testGetReason(
        iterable $results,
        ?Throwable $expected
    ): void {
        $subject = new CommitResult(
            new StagingResult(),
            ...$results
        );

        self::assertEquals($expected, $subject->getReason());
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function reasonProvider(): array
    {
        $fooReason = new RuntimeException('Foo');
        $barReason = new RuntimeException('Bar');

        return [
            'No results, No reason' => [
                'results' => [],
                'expected' => null
            ],
            'One result, No reason' => [
                'results' => [
                    new InvocationResult(
                        description: __METHOD__,
                        success: true,
                        invoked: true,
                        exception: null,
                        rollback: fn() => null
                    )
                ],
                'expected' => null
            ],
            'Many results, No reason' => [
                'results' => [
                    new InvocationResult(
                        description: __METHOD__,
                        success: true,
                        invoked: true,
                        exception: null,
                        rollback: fn() => null
                    ),
                    new InvocationResult(
                        description: __METHOD__,
                        success: true,
                        invoked: true,
                        exception: null,
                        rollback: fn() => null
                    ),
                    new InvocationResult(
                        description: __METHOD__,
                        success: true,
                        invoked: true,
                        exception: null,
                        rollback: fn() => null
                    )
                ],
                'expected' => null
            ],
            'One result, One reason' => [
                'results' => [
                    new InvocationResult(
                        description: __METHOD__,
                        success: true,
                        invoked: true,
                        exception: $fooReason,
                        rollback: fn() => null
                    )
                ],
                'expected' => $fooReason
            ],
            'Many results, One reason' => [
                'results' => [
                    new InvocationResult(
                        description: __METHOD__,
                        success: true,
                        invoked: true,
                        exception: null,
                        rollback: fn() => null
                    ),
                    new InvocationResult(
                        description: __METHOD__,
                        success: true,
                        invoked: true,
                        exception: null,
                        rollback: fn() => null
                    ),
                    new InvocationResult(
                        description: __METHOD__,
                        success: true,
                        invoked: true,
                        exception: $fooReason,
                        rollback: fn() => null
                    )
                ],
                'expected' => $fooReason
            ],
            'Many results, Many reasons' => [
                'results' => [
                    new InvocationResult(
                        description: __METHOD__,
                        success: true,
                        invoked: true,
                        exception: null,
                        rollback: fn() => null
                    ),
                    new InvocationResult(
                        description: __METHOD__,
                        success: true,
                        invoked: true,
                        exception: $barReason,
                        rollback: fn() => null
                    ),
                    new InvocationResult(
                        description: __METHOD__,
                        success: true,
                        invoked: true,
                        exception: $fooReason,
                        rollback: fn() => null
                    )
                ],
                'expected' => $barReason
            ]
        ];
    }

    /**
     * @covers ::rollback
     */
    public function testRollbackOnCommitted(): void
    {
        /** @var EventDispatcherInterface&MockObject $dispatcher */
        $dispatcher = self::createMock(EventDispatcherInterface::class);
        $subject = new CommitResult(new StagingResult());
        $subject->setDispatcher($dispatcher);

        $dispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->with(
                self::callback(
                    fn (RollbackBlockedEvent $event) => $event->committed
                )
            );

        $subject->rollback();
    }

    /**
     * @covers ::rollback
     */
    public function testDoubleRollback(): void
    {
        /** @var EventDispatcherInterface&MockObject $dispatcher */
        $dispatcher = self::createMock(EventDispatcherInterface::class);
        $subject = new CommitResult(
            new StagingResult(
                new StageResult(
                    staged: false,
                    requiresInvoke: true,
                    operation: self::createMock(OperationInterface::class)
                )
            )
        );
        $subject->setDispatcher($dispatcher);

        $dispatcher
            ->expects(self::exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [
                    self::isInstanceOf(RollbackResultEvent::class)
                ],
                [
                    self::callback(
                        fn (RollbackBlockedEvent $event) => $event->rolledBack
                    )
                ]
            );

        $subject->rollback();
        $subject->rollback();
    }

    /**
     * @dataProvider rollbackProvider
     *
     * @covers ::rollback
     * @covers ::getResults
     *
     * @param array<InvocationResult> $invocations
     */
    public function testRollback(array $invocations): void
    {
        /** @var EventDispatcherInterface&MockObject $dispatcher */
        $dispatcher = self::createMock(EventDispatcherInterface::class);
        $subject = new CommitResult(new StagingResult(), ...$invocations);
        $subject->setDispatcher($dispatcher);

        $expectedRollbacks = array_filter(
            $invocations,
            fn (InvocationResult $result) => $result->invoked
        );
        $numRollbacks = count($expectedRollbacks);

        $events = array_fill(
            0,
            $numRollbacks,
            self::isInstanceOf(RollbackEvent::class)
        );
        $events[] = self::callback(
            fn (RollbackResultEvent $event) => (
                count($event->rollbacks) === $numRollbacks
            )
        );

        $dispatcher
            ->expects(self::exactly($numRollbacks + 1))
            ->method('dispatch')
            ->withConsecutive(
                ...array_map(
                    fn ($assertion) => [$assertion],
                    $events
                )
            );

        $subject->rollback();
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function rollbackProvider(): array
    {
        return [
            'Single invocation' => [
                'invocations' => [
                    new InvocationResult(
                        description: __METHOD__,
                        success: false,
                        invoked: true,
                        exception: null,
                        rollback: fn () => null
                    )
                ]
            ],
            'Multiple invocations' => [
                'invocations' => [
                    new InvocationResult(
                        description: __METHOD__,
                        success: false,
                        invoked: true,
                        exception: null,
                        rollback: fn () => null
                    ),
                    new InvocationResult(
                        description: __METHOD__,
                        success: false,
                        invoked: true,
                        exception: null,
                        rollback: fn () => null
                    ),
                    new InvocationResult(
                        description: __METHOD__,
                        success: false,
                        invoked: true,
                        exception: null,
                        rollback: fn () => null
                    )
                ]
            ],
            'Partially invoked' => [
                'invocations' => [
                    new InvocationResult(
                        description: __METHOD__,
                        success: false,
                        invoked: false,
                        exception: null,
                        rollback: fn () => null
                    ),
                    new InvocationResult(
                        description: __METHOD__,
                        success: false,
                        invoked: true,
                        exception: null,
                        rollback: fn () => null
                    ),
                    new InvocationResult(
                        description: __METHOD__,
                        success: false,
                        invoked: true,
                        exception: null,
                        rollback: fn () => null
                    )
                ]
            ]
        ];
    }

    /**
     * @dataProvider rollbackProvider
     *
     * @covers ::rollback
     * @covers ::getResults
     *
     * @param array<InvocationResult> $invocations
     */
    public function testDefaultPreventedRollback(array $invocations): void
    {
        /** @var EventDispatcherInterface&MockObject $dispatcher */
        $dispatcher = self::createMock(EventDispatcherInterface::class);
        $subject = new CommitResult(new StagingResult(), ...$invocations);
        $subject->setDispatcher($dispatcher);

        $expectedRollbacks = array_filter(
            $invocations,
            fn (InvocationResult $result) => $result->invoked
        );
        $numRollbacks = count($expectedRollbacks);

        $events = array_fill(
            0,
            $numRollbacks,
            self::callback(
                function ($event = null): bool {
                    if ($event instanceof RollbackEvent) {
                        $event->preventDefault();

                        return true;
                    }

                    return false;
                }
            )
        );
        $events[] = self::callback(
            fn (RollbackResultEvent $event) => (
                count($event->rollbacks) === 0
            )
        );

        $dispatcher
            ->expects(self::exactly($numRollbacks + 1))
            ->method('dispatch')
            ->withConsecutive(
                ...array_map(
                    fn ($assertion) => [$assertion],
                    $events
                )
            );

        $subject->rollback();
    }

    /**
     * @dataProvider rollbackProvider
     *
     * @covers ::rollback
     * @covers ::getResults
     *
     * @param array<InvocationResult> $invocations
     */
    public function testCustomRollback(array $invocations): void
    {
        /** @var EventDispatcherInterface&MockObject $dispatcher */
        $dispatcher = self::createMock(EventDispatcherInterface::class);
        $subject = new CommitResult(new StagingResult(), ...$invocations);
        $subject->setDispatcher($dispatcher);

        $events = [
            self::callback(
                function ($event = null): bool {
                    if ($event instanceof RollbackEvent) {
                        return (string)$event === 'Transaction';
                    }

                    return false;
                }
            ),
            self::callback(
                fn (RollbackResultEvent $event) => (
                    count($event->rollbacks) === 1
                )
            )
        ];

        $dispatcher
            ->expects(self::exactly(count($events)))
            ->method('dispatch')
            ->withConsecutive(
                ...array_map(
                    fn ($assertion) => [$assertion],
                    $events
                )
            );

        $transactionRollbackRan = false;

        $subject->rollback(
            function () use (&$transactionRollbackRan): void {
                $transactionRollbackRan = true;
            }
        );

        self::assertTrue(
            $transactionRollbackRan,
            'Transaction rollback must run.'
        );
    }
}
