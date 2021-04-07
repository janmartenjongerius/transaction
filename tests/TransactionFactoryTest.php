<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Tests;

use Johmanx10\Transaction\DryRun;
use Johmanx10\Transaction\Operation\OperationInterface;
use Johmanx10\Transaction\Transaction;
use Johmanx10\Transaction\TransactionFactory;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @coversDefaultClass \Johmanx10\Transaction\TransactionFactory
 */
class TransactionFactoryTest extends TestCase
{
    use Matrix;

    /**
     * @dataProvider argumentsProvider
     *
     * @param EventDispatcherInterface|null $dispatcher
     * @param bool                          $strategy
     * @param OperationInterface[]          $operations
     *
     * @covers ::__construct
     * @covers ::__invoke
     */
    public function testFactory(
        ?EventDispatcherInterface $dispatcher,
        bool $strategy,
        iterable $operations
    ) {
        $subject = new TransactionFactory($dispatcher, $strategy);
        $this->assertInstanceOf(TransactionFactory::class, $subject);
        $this->assertInstanceOf(
            match ($strategy) {
                TransactionFactory::STRATEGY_DRY_RUN => DryRun::class,
                TransactionFactory::STRATEGY_EXECUTE => Transaction::class
            },
            $subject(...$operations)
        );
    }

    public function argumentsProvider(): array
    {
        return static::createMatrix(
            [
                'dispatcher' => [
                    null,
                    $this->createMock(EventDispatcherInterface::class)
                ],
                'strategy' => [
                    TransactionFactory::STRATEGY_EXECUTE,
                    TransactionFactory::STRATEGY_DRY_RUN
                ],
                'operations' => [
                    [],
                    [
                        $this->createMock(OperationInterface::class)
                    ],
                    [
                        $this->createMock(OperationInterface::class),
                        $this->createMock(OperationInterface::class),
                        $this->createMock(OperationInterface::class)
                    ]
                ]
            ]
        );
    }
}
