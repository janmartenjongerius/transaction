<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Event;

use Johmanx10\Transaction\Event\CommitResultEvent;
use Johmanx10\Transaction\Event\RollbackBlockedEvent;
use Johmanx10\Transaction\Event\RollbackResultEvent;
use Johmanx10\Transaction\Event\StagingResultEvent;
use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Operation\Result\InvocationResult;
use Johmanx10\Transaction\Operation\Result\StageResult;
use Johmanx10\Transaction\Operation\Rollback;
use Johmanx10\Transaction\Result\CommitResult;
use Johmanx10\Transaction\Result\StagingResult;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Event\TransactionLoggerSubscriber;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Event\TransactionLoggerSubscriber
 */
class TransactionLoggerSubscriberTest extends TestCase
{
    use EventSubscriberTrait;

    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(
            TransactionLoggerSubscriber::class,
            new TransactionLoggerSubscriber(
                logger: self::createMock(LoggerInterface::class)
            )
        );
    }

    /**
     * @covers ::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertThatEventsWillBeListenedFor(
            TransactionLoggerSubscriber::class,
            TransactionLoggerSubscriber::getSubscribedEvents()
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
            subscriberCallback: fn(LoggerInterface $logger) => new TransactionLoggerSubscriber($logger),
            eventCallback: $eventCallback,
            message: $message
        );
    }

    /**
     * @dataProvider rollbackBlockedProvider
     *
     * @covers ::onRollbackBlocked
     *
     * @param RollbackBlockedEvent       $event
     * @param array<array<string,mixed>> $expected
     */
    public function testOnRollbackBlocked(
        RollbackBlockedEvent $event,
        array $expected
    ): void {
        self::assertEventCausesRecords(
            $expected,
            fn(TransactionLoggerSubscriber $subscriber) => $subscriber->onRollbackBlocked($event)
        );
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function rollbackBlockedProvider(): array
    {
        return [
            'Both committed and rolled back.' => [
                'event' => new RollbackBlockedEvent(
                    rolledBack: true,
                    committed: true
                ),
                'expected' => [
                    self::createRecord(
                        message: 'Rollback was not allowed to proceed.',
                        level: 'warning',
                        context: [
                            'committed' => true,
                            'rolledBack' => true
                        ]
                    ),
                    self::createRecord('* Transaction successfully committed'),
                    self::createRecord('* Transaction previously rolled back')
                ]
            ],
            'Committed, not rolled back.' => [
                'event' => new RollbackBlockedEvent(
                    rolledBack: false,
                    committed: true
                ),
                'expected' => [
                    self::createRecord(
                        message: 'Rollback was not allowed to proceed.',
                        level: 'warning',
                        context: [
                            'committed' => true,
                            'rolledBack' => false
                        ]
                    ),
                    self::createRecord('* Transaction successfully committed')
                ]
            ],
            'Not committed, rolled back.' => [
                'event' => new RollbackBlockedEvent(
                    rolledBack: true,
                    committed: false
                ),
                'expected' => [
                    self::createRecord(
                        message: 'Rollback was not allowed to proceed.',
                        level: 'warning',
                        context: [
                            'committed' => false,
                            'rolledBack' => true
                        ]
                    ),
                    self::createRecord('* Transaction previously rolled back')
                ]
            ],
            'Neither committed, nor rolled back.' => [
                'event' => new RollbackBlockedEvent(
                    rolledBack: false,
                    committed: false
                ),
                'expected' => [
                    self::createRecord(
                        message: 'Rollback was not allowed to proceed.',
                        level: 'warning',
                        context: [
                            'committed' => false,
                            'rolledBack' => false
                        ]
                    )
                ]
            ]
        ];
    }

    /**
     * @dataProvider stagingResultProvider
     *
     * @covers ::onAfterStaging
     *
     * @param StagingResultEvent         $event
     * @param array<array<string,mixed>> $expected
     */
    public function testOnAfterStaging(
        StagingResultEvent $event,
        array $expected
    ): void {
        self::assertEventCausesRecords(
            $expected,
            fn(TransactionLoggerSubscriber $subscriber
            ) => $subscriber->onAfterStaging($event)
        );
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function stagingResultProvider(): array
    {
        return [
            'Staged' => [
                'event' => new StagingResultEvent(
                    new StagingResult()
                ),
                'expected' => [
                    self::createRecord(
                        message: 'Transaction staged',
                        level: 'info'
                    )
                ]
            ],
            'Not staged' => [
                'event' => new StagingResultEvent(
                    new StagingResult(
                        new StageResult(
                            staged: false,
                            requiresInvoke: true,
                            operation: self::createMock(OperationInterface::class)
                        )
                    )
                ),
                'expected' => [
                    self::createRecord(
                        message: 'Transaction could not be staged.',
                        level: 'warning'
                    )
                ]
            ]
        ];
    }

    /**
     * @dataProvider commitResultProvider
     *
     * @covers ::onAfterCommit
     *
     * @param CommitResultEvent          $event
     * @param array<array<string,mixed>> $expected
     */
    public function testOnAfterCommit(
        CommitResultEvent $event,
        array $expected
    ): void {
        self::assertEventCausesRecords(
            $expected,
            fn(TransactionLoggerSubscriber $subscriber
            ) => $subscriber->onAfterCommit($event)
        );
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function commitResultProvider(): array
    {
        $exceptionMessage = sprintf('Inside %s', __METHOD__);

        return [
            'Committed, no exception' => [
                'event' => new CommitResultEvent(
                    new CommitResult(
                        new StagingResult()
                    )
                ),
                'expected' => [
                    self::createRecord(
                        message: 'Transaction committed',
                        level: 'info'
                    )
                ]
            ],
            'Committed, with exception' => [
                'event' => new CommitResultEvent(
                    new CommitResult(
                        staging: new StagingResult(),
                        results: new InvocationResult(
                            description: __METHOD__,
                            success: true,
                            invoked: true,
                            exception: new RuntimeException($exceptionMessage),
                            rollback: fn () => null
                        )
                    )
                ),
                'expected' => [
                    self::createRecord(
                        message: $exceptionMessage,
                        level: 'error'
                    ),
                    self::createRecord(
                        message: 'Transaction committed',
                        level: 'info'
                    )
                ]
            ],
            'Not committed, no exception' => [
                'event' => new CommitResultEvent(
                    new CommitResult(
                        staging: new StagingResult(),
                        results: new InvocationResult(
                            description: __METHOD__,
                            success: false,
                            invoked: true,
                            exception: null,
                            rollback: fn () => null
                        )
                    )
                ),
                'expected' => [
                    self::createRecord(
                        message: 'Transaction not committed',
                        level: 'error'
                    )
                ]
            ],
            'Not committed, with exception' => [
                'event' => new CommitResultEvent(
                    new CommitResult(
                        staging: new StagingResult(),
                        results: new InvocationResult(
                            description: __METHOD__,
                            success: false,
                            invoked: true,
                            exception: new RuntimeException($exceptionMessage),
                            rollback: fn () => null
                        )
                    )
                ),
                'expected' => [
                    self::createRecord(
                        message: $exceptionMessage,
                        level: 'error'
                    ),
                    self::createRecord(
                        message: 'Transaction not committed',
                        level: 'error'
                    )
                ]
            ]
        ];
    }

    /**
     * @dataProvider rollbackResultProvider
     *
     * @covers ::onAfterRollback
     *
     * @param RollbackResultEvent $event
     * @param array<array<string,mixed>> $expected
     */
    public function testOnAfterRollback(
        RollbackResultEvent $event,
        array $expected
    ): void {
        self::assertEventCausesRecords(
            $expected,
            fn(TransactionLoggerSubscriber $subscriber) => $subscriber->onAfterRollback($event)
        );
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function rollbackResultProvider(): array
    {
        return [
            'No rollbacks' => [
                'event' => new RollbackResultEvent(),
                'expected' => [
                    self::createRecord(
                        message: 'Performed 0 rollback(s)',
                        level: 'info'
                    )
                ]
            ],
            'One rollback' => [
                'event' => new RollbackResultEvent(
                    self::createRollback()
                ),
                'expected' => [
                    self::createRecord(
                        message: 'Performed 1 rollback(s)',
                        level: 'info'
                    )
                ]
            ],
            'Many rollbacks' => [
                'event' => new RollbackResultEvent(
                    self::createRollback(),
                    self::createRollback(),
                    self::createRollback()
                ),
                'expected' => [
                    self::createRecord(
                        message: 'Performed 3 rollback(s)',
                        level: 'info'
                    )
                ]
            ]
        ];
    }

    private static function createRollback(): Rollback
    {
        return new Rollback(
            description: __CLASS__,
            rollback: fn () => null
        );
    }
}
