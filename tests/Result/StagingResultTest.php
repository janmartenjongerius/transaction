<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Result;

use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Operation\Result\StageResult;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Result\StagingResult;
use Traversable;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Result\StagingResult
 */
class StagingResultTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        foreach ([0, 1, 3] as $numResults) {
            $results = array_fill(
                start_index: 0,
                count: $numResults,
                value: new StageResult(
                    true,
                    true,
                    self::createMock(OperationInterface::class)
                )
            );

            self::assertInstanceOf(
                StagingResult::class,
                new StagingResult(...$results)
            );
        }
    }

    /**
     * @dataProvider requiredProvider
     *
     * @covers ::getRequiredOperations
     *
     * @param array<StageResult> $results
     * @param int                $expected
     */
    public function testGetRequiredOperations(array $results, int $expected): void
    {
        $subject = new StagingResult(...$results);
        self::assertCount(
            $expected,
            $subject->getRequiredOperations(),
            'The required operations, and only the required operations, must all be present.'
        );
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function requiredProvider(): array
    {
        $operation = self::createMock(OperationInterface::class);

        return [
            'No results' => [
                'results' => [],
                'expected' => 0
            ],
            'Single result, required' => [
                'results' => [
                    new StageResult(
                        staged: true,
                        requiresInvoke: true,
                        operation: $operation
                    )
                ],
                'expected' => 1
            ],
            'Single result, not required' => [
                'results' => [
                    new StageResult(
                        staged: true,
                        requiresInvoke: false,
                        operation: $operation
                    )
                ],
                'expected' => 0
            ],
            'Multiple results, all required' => [
                'results' => [
                    new StageResult(
                        staged: true,
                        requiresInvoke: true,
                        operation: $operation
                    ),
                    new StageResult(
                        staged: true,
                        requiresInvoke: true,
                        operation: $operation
                    ),
                    new StageResult(
                        staged: true,
                        requiresInvoke: true,
                        operation: $operation
                    )
                ],
                'expected' => 3
            ],
            'Multiple results, some required' => [
                'results' => [
                    new StageResult(
                        staged: true,
                        requiresInvoke: true,
                        operation: $operation
                    ),
                    new StageResult(
                        staged: false,
                        requiresInvoke: false,
                        operation: $operation
                    ),
                    new StageResult(
                        staged: false,
                        requiresInvoke: true,
                        operation: $operation
                    )
                ],
                'expected' => 2
            ],
            'Multiple results, none required' => [
                'results' => [
                    new StageResult(
                        staged: true,
                        requiresInvoke: false,
                        operation: $operation
                    ),
                    new StageResult(
                        staged: false,
                        requiresInvoke: false,
                        operation: $operation
                    ),
                    new StageResult(
                        staged: false,
                        requiresInvoke: false,
                        operation: $operation
                    )
                ],
                'expected' => 0
            ]
        ];
    }

    /**
     * @dataProvider isStagedProvider
     *
     * @covers ::isStaged
     *
     * @param array<StageResult> $results
     * @param bool               $expected
     */
    public function testIsStaged(array $results, bool $expected): void
    {
        $subject = new StagingResult(...$results);
        self::assertSame($expected, $subject->isStaged());
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function isStagedProvider(): array
    {
        $operation = self::createMock(OperationInterface::class);

        return [
            'No results' => [
                'results' => [],
                'staged' => true
            ],
            'Single result, staged' => [
                'results' => [
                    new StageResult(
                        staged: true,
                        requiresInvoke: true,
                        operation: $operation
                    )
                ],
                'staged' => true
            ],
            'Single result, not staged' => [
                'results' => [
                    new StageResult(
                        staged: false,
                        requiresInvoke: true,
                        operation: $operation
                    )
                ],
                'staged' => false
            ],
            'Single result, optional' => [
                'results' => [
                    new StageResult(
                        staged: false,
                        requiresInvoke: false,
                        operation: $operation
                    )
                ],
                'staged' => true
            ],
            'Multiple results, staged' => [
                'results' => [
                    new StageResult(
                        staged: true,
                        requiresInvoke: true,
                        operation: $operation
                    ),
                    new StageResult(
                        staged: true,
                        requiresInvoke: true,
                        operation: $operation
                    ),
                    new StageResult(
                        staged: true,
                        requiresInvoke: true,
                        operation: $operation
                    )
                ],
                'staged' => true
            ],
            'Multiple results, not staged' => [
                'results' => [
                    new StageResult(
                        staged: true,
                        requiresInvoke: true,
                        operation: $operation
                    ),
                    new StageResult(
                        staged: true,
                        requiresInvoke: false,
                        operation: $operation
                    ),
                    new StageResult(
                        staged: false,
                        requiresInvoke: true,
                        operation: $operation
                    ),
                    new StageResult(
                        staged: false,
                        requiresInvoke: false,
                        operation: $operation
                    )
                ],
                'staged' => false
            ]
        ];
    }
}
