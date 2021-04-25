<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests;

use Johmanx10\Transaction\DispatcherAware;
use Johmanx10\Transaction\Operation\Event\InvocationEvent;
use Johmanx10\Transaction\Operation\Event\InvocationResultEvent;
use Johmanx10\Transaction\Operation\Event\StageResultEvent;
use Johmanx10\Transaction\Operation\Invocation;
use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Operation\Result\InvocationResult;
use Johmanx10\Transaction\Operation\Result\StageResult;
use Johmanx10\Transaction\TransactionInterface;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Committable;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Committable
 */
class CommittableTest extends TestCase
{
    use Operation;

    private function createSubject(
        ?EventDispatcherInterface $dispatcher,
        OperationInterface ...$operations
    ): TransactionInterface {
        $subject = new class ($operations) implements TransactionInterface {
            use Committable;
            use DispatcherAware;

            public function __construct(private array $operations)
            {
            }

            private function invoke(Invocation $invocation): InvocationResult
            {
                return $invocation();
            }
        };
        $subject->setDispatcher($dispatcher);

        return $subject;
    }

    /**
     * @dataProvider operationsProvider
     *
     * @param bool               $expected
     * @param OperationInterface ...$operations
     *
     * @covers ::commit
     * @covers ::stage
     */
    public function testCommit(
        bool $expected,
        OperationInterface ...$operations
    ): void {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $subject = $this->createSubject($dispatcher, ...$operations);

        $stats = [
            'invocations' => [
                'expected' => 0,
                'actual' => 0
            ]
        ];

        $dispatcher
            ->expects(self::any())
            ->method('dispatch')
            ->with(
                self::callback(
                    function (object $event) use (&$stats): bool {
                        switch (get_class($event)) {
                            case StageResultEvent::class:
                                /** @var StageResult $result */
                                $result = $event->result;
                                $stats['invocations']['expected'] += (
                                    $result->staged || !$result->requiresInvoke
                                        ? 1
                                        : 0
                                );
                        }

                        return true;
                    }
                )
            );

        foreach ($stats as $group => $measurements) {
            $this->assertEquals(
                $measurements['expected'],
                $measurements['actual'],
                sprintf(
                    'Expected "%s" does not match actual measurement.',
                    $group
                )
            );
        }

        $result = $subject->commit();
        $this->assertEquals($expected, $result->committed());
    }

    public function operationsProvider(): array
    {
        return [
            [true],
            [
                false,
                $this->createOperation(willStage: false)
            ],
            [
                false,
                $this->createOperation(willRun: false)
            ],
            [
                true,
                $this->createOperation()
            ],
            [
                true,
                $this->createOperation(willStage: null)
            ],
            [
                false,
                $this->createOperation(willStage: null),
                $this->createOperation(willStage: false),
                $this->createOperation()
            ],
            [
                false,
                $this->createOperation(),
                $this->createOperation(),
                $this->createOperation(willRun: false)
            ]
        ];
    }

    /**
     * @covers ::commit
     */
    public function testCommitPreventedOperations(): void
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $subject = $this->createSubject(
            $dispatcher,
            $this->createOperation(willRun: false)
        );

        $numResults = 0;

        $dispatcher
            ->expects(self::any())
            ->method('dispatch')
            ->with(self::isType('object'))
            ->willReturnCallback(
                function (object $event) use (&$numResults): object {
                    if ($event instanceof InvocationEvent) {
                        $event->preventDefault();
                    }

                    if ($event instanceof InvocationResultEvent) {
                        $numResults++;
                    }

                    return $event;
                }
            );

        $subject->commit();
        $this->assertEquals(
            0,
            $numResults,
            'Invocation result should never exist.'
        );
    }
}
