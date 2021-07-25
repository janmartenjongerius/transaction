<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Operation;

use Closure;
use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Operation\Result\StageResult;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Operation\Stage;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Operation\Stage
 */
class StageTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(
            Stage::class,
            new Stage(
                self::createMock(OperationInterface::class),
                fn () => null
            )
        );
    }

    /**
     * @dataProvider stageProvider
     *
     * @covers ::__invoke
     *
     * @param Closure $stage
     * @param bool    $staged
     * @param bool    $requiresInvoke
     */
    public function testInvoke(
        Closure $stage,
        bool $staged,
        bool $requiresInvoke
    ): void {
        $subject = new Stage(
            self::createMock(OperationInterface::class),
            $stage
        );

        $result = $subject->__invoke();
        self::assertInstanceOf(StageResult::class, $result);
        self::assertEquals($staged, $result->staged);
        self::assertEquals($requiresInvoke, $result->requiresInvoke);
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public function stageProvider(): array
    {
        return [
            'Staged' => [
                'stage' => fn () => StageResult::RESULT_STAGED,
                'staged' => true,
                'requiresInvoke' => true
            ],
            'Failed' => [
                'stage' => fn () => StageResult::RESULT_FAILED,
                'staged' => false,
                'requiresInvoke' => true
            ],
            'Skipped' => [
                'stage' => fn () => StageResult::RESULT_SKIP,
                'staged' => false,
                'requiresInvoke' => false
            ]
        ];
    }
}
