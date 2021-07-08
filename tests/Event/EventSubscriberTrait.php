<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Tests\Event;

use Psr\Log\Test\TestLogger;
use ReflectionClass;

trait EventSubscriberTrait
{
    private static function assertThatEventsWillBeListenedFor(
        string $class,
        array $map
    ): void {
        $reflection = new ReflectionClass($class);

        foreach ($map as $eventClass => $method) {
            self::assertTrue(
                $reflection->hasMethod($method),
                sprintf(
                    'The subscriber must have method "%s"',
                    $method
                )
            );

            if (!$reflection->hasMethod($method)) {
                continue;
            }

            $reflectionMethod = $reflection->getMethod($method);

            self::assertGreaterThanOrEqual(
                1,
                $reflectionMethod->getNumberOfRequiredParameters(),
                'Must have minimum number of required parameters.'
            );

            if ($reflectionMethod->getNumberOfRequiredParameters() < 1) {
                continue;
            }

            [$parameter] = $reflectionMethod->getParameters();

            self::assertFalse(
                $parameter->isOptional(),
                'Parameter cannot be optional.'
            );

            self::assertTrue(
                is_a(
                    object_or_class: $eventClass,
                    class: $parameter->getType()?->getName(),
                    allow_string: true
                ),
                sprintf(
                    'Parameter type must implement event class "%s"',
                    $eventClass
                )
            );
        }
    }

    /**
     * @param string              $message
     * @param string              $level
     * @param array<string,mixed> $context
     *
     * @return array<string,mixed>
     */
    private static function createRecord(
        string $message,
        string $level = 'debug',
        array $context = []
    ): array {
        return [
            'level' => $level,
            'message' => $message,
            'context' => $context
        ];
    }

    /**
     * @param array<array<string,mixed>> $expected
     * @param callable                   $subscriberCallback
     * @param callable                   $eventCallback
     * @param string                     $message
     */
    private static function assertSubscriberCausesRecords(
        array $expected,
        callable $subscriberCallback,
        callable $eventCallback,
        string $message = ''
    ): void {
        $logger = new TestLogger();

        self::assertEquals(
            [],
            $logger->records,
            'Logger should have no records before calling event.'
        );

        $eventCallback(
            $subscriberCallback($logger)
        );

        self::assertEquals($expected, $logger->records, $message);
    }
}
