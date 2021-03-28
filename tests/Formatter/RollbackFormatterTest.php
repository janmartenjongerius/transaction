<?php


namespace Johmanx10\Transaction\Tests\Formatter;

use Exception;
use Johmanx10\Transaction\Exception\TransactionRolledBackExceptionInterface;
use Johmanx10\Transaction\Formatter\OperationFailureFormatterInterface;
use Johmanx10\Transaction\OperationFailureInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Johmanx10\Transaction\Formatter\RollbackFormatter;

/**
 * @coversDefaultClass \Johmanx10\Transaction\Formatter\RollbackFormatter
 */
class RollbackFormatterTest extends TestCase
{
    private const FAILURE = 'Failure';

    /**
     * @return void
     *
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $this->assertInstanceOf(
            RollbackFormatter::class,
            new RollbackFormatter()
        );

        $this->assertInstanceOf(
            RollbackFormatter::class,
            new RollbackFormatter(
                $this->createMock(OperationFailureFormatterInterface::class)
            )
        );
    }

    /**
     * @dataProvider emptyRollbackProvider
     * @dataProvider filledRollbackProvider
     *
     * @param TransactionRolledBackExceptionInterface $rollback
     * @param string                                  $expected
     *
     * @return void
     *
     * @covers ::format
     */
    public function testFormat(
        TransactionRolledBackExceptionInterface $rollback,
        string $expected
    ): void {
        /** @var OperationFailureFormatterInterface|MockObject $formatter */
        $formatter = $this->createMock(OperationFailureFormatterInterface::class);

        $subject = new RollbackFormatter($formatter);

        $formatter
            ->expects(self::any())
            ->method('format')
            ->willReturn(static::FAILURE);

        $this->assertEquals($expected, $subject->format($rollback));
    }

    /**
     * @return array
     */
    public function emptyRollbackProvider(): array
    {
        $rollback = new class (
            'Could not properly execute operation.'
        ) extends Exception implements TransactionRolledBackExceptionInterface
        {
            /**
             * Get the failed operations.
             *
             * @return OperationFailureInterface[]
             */
            public function getFailures(): array
            {
                return [];
            }
        };

        return [
            [
                $rollback,
                <<<'MESSAGE'
Could not properly execute operation.

Stacktrace:
MESSAGE
            ]
        ];
    }

    /**
     * @return array
     */
    public function filledRollbackProvider(): array
    {
        $rollback = new class (
            'Could not properly execute operation.',
            $this->createMock(OperationFailureInterface::class),
            $this->createMock(OperationFailureInterface::class),
            $this->createMock(OperationFailureInterface::class)
        ) extends Exception implements TransactionRolledBackExceptionInterface
        {
            /** @var OperationFailureInterface[] */
            private $failures;

            /**
             * Constructor.
             *
             * @param string                    $message
             * @param OperationFailureInterface ...$failures
             */
            public function __construct(
                string $message,
                OperationFailureInterface ...$failures
            ) {
                $this->failures = $failures;
                parent::__construct($message, 0, null);
            }

            /**
             * Get the failed operations.
             *
             * @return OperationFailureInterface[]
             */
            public function getFailures(): array
            {
                return $this->failures;
            }
        };

        $message = static::FAILURE;

        return [
            [
                $rollback,
                <<<MESSAGE
Could not properly execute operation.

Stacktrace:
$message
$message
$message
MESSAGE
            ]
        ];
    }
}
