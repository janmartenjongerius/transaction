<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Operation\Event;

use Johmanx10\Transaction\Event\DefaultPreventable;
use Johmanx10\Transaction\Event\DefaultPreventableInterface;
use Johmanx10\Transaction\Operation\Event\InvocationEvent;
use Johmanx10\Transaction\Operation\Event\InvocationResultEvent;
use Johmanx10\Transaction\Operation\Event\RollbackEvent;
use Johmanx10\Transaction\Operation\Event\StageEvent;
use Johmanx10\Transaction\Operation\Event\StageResultEvent;
use Johmanx10\Transaction\Operation\Invocation;
use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Operation\Result\InvocationResult;
use Johmanx10\Transaction\Operation\Result\StageResult;
use Johmanx10\Transaction\Operation\Rollback;
use Johmanx10\Transaction\Operation\Stage;
use Johmanx10\Transaction\Tests\Event\EventSubscriberTrait;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Operation\Event\OperationLoggerSubscriber;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Operation\Event\OperationLoggerSubscriber
 */
class OperationLoggerSubscriberTest extends TestCase
{
    use EventSubscriberTrait;

    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(
            OperationLoggerSubscriber::class,
            new OperationLoggerSubscriber(
                self::createMock(LoggerInterface::class)
            )
        );
    }

    /**
     * @covers ::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertThatEventsWillBeListenedFor(
            OperationLoggerSubscriber::class,
            OperationLoggerSubscriber::getSubscribedEvents()
        );
    }

    /**
     * @param array<array<string,mixed>> $expected
     * @param callable                   $eventCallback
     * @param string                     $message
     */
    private static function assertEventCausesRecords(
        array $expected,
        callable $eventCallback,
        string $message = ''
    ): void {
        self::assertSubscriberCausesRecords(
            expected: $expected,
            subscriberCallback: fn(LoggerInterface $logger) => new OperationLoggerSubscriber($logger),
            eventCallback: $eventCallback,
            message: $message
        );
    }

    /**
     * @dataProvider preventableProvider
     *
     * @covers ::onAfterPrevent
     *
     * @param DefaultPreventableInterface $event
     * @param array<array<string,mixed>>  $expected
     */
    public function testOnAfterPrevent(
        DefaultPreventableInterface $event,
        array $expected
    ): void {
        self::assertEventCausesRecords(
            [],
            fn(OperationLoggerSubscriber $subscriber) => $subscriber->onAfterPrevent($event)
        );

        $event->preventDefault();

        self::assertEventCausesRecords(
            $expected,
            fn(OperationLoggerSubscriber $subscriber) => $subscriber->onAfterPrevent($event)
        );
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function preventableProvider(): array
    {
        $operation = self::createMock(OperationInterface::class);
        $operation
            ->expects(self::any())
            ->method('__toString')
            ->willReturn(__METHOD__);

        return [
            'Stage event' => [
                'event' => new StageEvent(
                    new Stage($operation, fn () => null)
                ),
                'expected' => [
                    self::createRecord(
                        '[stage] Prevented: ' . __METHOD__
                    )
                ]
            ],
            'Rollback event, without reason' => [
                'event' => new RollbackEvent(
                    new Rollback(__METHOD__, fn () => null),
                    null
                ),
                'expected' => [
                    self::createRecord(
                        '[rollback] Prevented: ' . __METHOD__
                    )
                ]
            ],
            'Rollback event, with reason' => [
                'event' => new RollbackEvent(
                    new Rollback(__METHOD__, fn () => null),
                    new RuntimeException(__METHOD__)
                ),
                'expected' => [
                    self::createRecord(
                        '[rollback] Prevented: ' . __METHOD__
                    )
                ]
            ],
            'Invocation event' => [
                'event' => new InvocationEvent(
                    new Invocation(
                        __METHOD__,
                        fn () => null,
                        fn () => null
                    )
                ),
                'expected' => [
                    self::createRecord(
                        '[invoke] Prevented: ' . __METHOD__
                    )
                ]
            ],
            'Anonymous class' => [
                'event' => new class implements DefaultPreventableInterface {
                    use DefaultPreventable;
                },
                'expected' => [
                    self::createRecord(
                        '[defaultpreventableinterface] Prevented: '
                        . DefaultPreventableInterface::class
                    )
                ]
            ]
        ];
    }

    /**
     * @dataProvider invocationResultProvider
     *
     * @covers ::onAfterInvoke
     *
     * @param InvocationResultEvent      $event
     * @param array<array<string,mixed>> $expected
     */
    public function testOnAfterInvoke(
        InvocationResultEvent $event,
        array $expected
    ): void {
        self::assertEventCausesRecords(
            $expected,
            fn(OperationLoggerSubscriber $subscriber) => $subscriber->onAfterInvoke($event)
        );
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function invocationResultProvider(): array
    {
        return [
            'Failed, with exception' => [
                'event' => new InvocationResultEvent(
                    new InvocationResult(
                        description: __METHOD__,
                        success: false,
                        invoked: true,
                        exception: new RuntimeException(__METHOD__),
                        rollback: fn () => null
                    )
                ),
                'expected' => [
                    self::createRecord(__METHOD__, 'error'),
                    self::createRecord('[invoke] Failed: ' . __METHOD__, 'error')
                ]
            ],
            'Failed, without exception' => [
                'event' => new InvocationResultEvent(
                    new InvocationResult(
                        description: __METHOD__,
                        success: false,
                        invoked: true,
                        exception: null,
                        rollback: fn () => null
                    )
                ),
                'expected' => [
                    self::createRecord('[invoke] Failed: ' . __METHOD__, 'error')
                ]
            ],
            'Skipped' => [
                'event' => new InvocationResultEvent(
                    new InvocationResult(
                        description: __METHOD__,
                        success: false,
                        invoked: false,
                        exception: null,
                        rollback: fn () => null
                    )
                ),
                'expected' => [
                    self::createRecord('[invoke] Skipped: ' . __METHOD__)
                ]
            ],
            'Success' => [
                'event' => new InvocationResultEvent(
                    new InvocationResult(
                        description: __METHOD__,
                        success: true,
                        invoked: true,
                        exception: null,
                        rollback: fn () => null
                    )
                ),
                'expected' => [
                    self::createRecord('[invoke] Success: ' . __METHOD__, 'info')
                ]
            ],
            'Success, with exception' => [
                'event' => new InvocationResultEvent(
                    new InvocationResult(
                        description: __METHOD__,
                        success: true,
                        invoked: true,
                        exception: new RuntimeException(__METHOD__),
                        rollback: fn () => null
                    )
                ),
                'expected' => [
                    self::createRecord(__METHOD__, 'error'),
                    self::createRecord('[invoke] Success: ' . __METHOD__, 'info')
                ]
            ]
        ];
    }

    /**
     * @dataProvider rollbackProvider
     *
     * @covers ::onRollback
     *
     * @param RollbackEvent              $event
     * @param array<array<string,mixed>> $expected
     */
    public function testOnRollback(RollbackEvent $event, array $expected): void
    {
        self::assertEventCausesRecords(
            $expected,
            fn(OperationLoggerSubscriber $subscriber) => $subscriber->onRollback($event)
        );
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function rollbackProvider(): array
    {
        return [
            'Without reason' => [
                'event' => new RollbackEvent(
                    rollback: new Rollback(__METHOD__, fn () => null),
                    reason: null
                ),
                'expected' => [
                    self::createRecord(
                        '[rollback] Rolling back: ' . __METHOD__,
                        'warning'
                    )
                ]
            ],
            'With reason' => [
                'event' => new RollbackEvent(
                    rollback: new Rollback(__METHOD__, fn () => null),
                    reason: new RuntimeException(__METHOD__)
                ),
                'expected' => [
                    self::createRecord(
                        '[rollback] Rolling back: ' . __METHOD__,
                        'warning'
                    ),
                    self::createRecord(__METHOD__)
                ]
            ]
        ];
    }

    /**
     * @covers ::onStage
     */
    public function testOnStage(): void
    {
        $operation = self::createMock(OperationInterface::class);
        $operation
            ->expects(self::any())
            ->method('__toString')
            ->willReturn(__METHOD__);

        self::assertEventCausesRecords(
            [
                self::createRecord('[stage] Staging: ' . __METHOD__)
            ],
            fn(OperationLoggerSubscriber $subscriber) => $subscriber->onStage(
                new StageEvent(
                    new Stage($operation, fn () => null)
                )
            )
        );
    }

    /**
     * @dataProvider stageResultProvider
     *
     * @covers ::onAfterStage
     *
     * @param StageResultEvent           $event
     * @param array<array<string,mixed>> $expected
     */
    public function testOnAfterStage(
        StageResultEvent $event,
        array $expected
    ): void {
        self::assertEventCausesRecords(
            $expected,
            fn(OperationLoggerSubscriber $subscriber) => $subscriber->onAfterStage($event)
        );
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function stageResultProvider(): array
    {
        $operation = self::createMock(OperationInterface::class);
        $operation
            ->expects(self::any())
            ->method('__toString')
            ->willReturn(__METHOD__);

        return [
            'Staged' => [
                'event' => new StageResultEvent(
                    new StageResult(
                        true,
                        true,
                        $operation
                    )
                ),
                'expected' => [
                    self::createRecord('[stage] Staged: ' . __METHOD__, 'info')
                ]
            ],
            'Not staged' => [
                'event' => new StageResultEvent(
                    new StageResult(
                        false,
                        true,
                        $operation
                    )
                ),
                'expected' => [
                    self::createRecord(
                        '[stage] Not staged: ' . __METHOD__,
                        'warning'
                    )
                ]
            ],
            'Not required' => [
                'event' => new StageResultEvent(
                    new StageResult(
                        false,
                        false,
                        $operation
                    )
                ),
                'expected' => [
                    self::createRecord('[stage] Not required: ' . __METHOD__)
                ]
            ]
        ];
    }

    /**
     * @covers ::onInvoke
     */
    public function testOnInvoke(): void
    {
        self::assertEventCausesRecords(
            [
                self::createRecord('[invoke] Test', 'info')
            ],
            fn(OperationLoggerSubscriber $subscriber) => $subscriber->onInvoke(
                new InvocationEvent(
                    new Invocation(
                        'Test',
                        fn () => null,
                        fn () => null
                    )
                )
            )
        );
    }
}
