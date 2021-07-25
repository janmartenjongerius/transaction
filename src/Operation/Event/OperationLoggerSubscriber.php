<?php

declare(strict_types=1);

namespace Johmanx10\Transaction\Operation\Event;

use Johmanx10\Transaction\Event\DefaultPreventableInterface;
use Psr\Log\LoggerInterface;
use Stringable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OperationLoggerSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    /**
     * @inheritDoc
     *
     * @return array<string,string|array<array{0: string, 1?: int}>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            InvocationEvent::class => [['onInvoke'], ['onAfterPrevent', -1]],
            InvocationResultEvent::class => 'onAfterInvoke',
            RollbackEvent::class => [['onRollback'], ['onAfterPrevent', -1]],
            StageEvent::class => [['onStage'], ['onAfterPrevent', -1]],
            StageResultEvent::class => 'onAfterStage'
        ];
    }

    public function onAfterPrevent(DefaultPreventableInterface $event): void
    {
        if ($event->isDefaultPrevented()) {
            $class = (string)preg_replace(
                '/^([^@]+).*$/',
                '$1',
                get_class($event)
            );
            $this->logger->debug(
                sprintf(
                    "[%s] Prevented: %s",
                    // @codeCoverageIgnoreStart
                    // For some reason, the first line of the match will not be
                    // covered. Since the body of the match is covered, this is
                    // ignored. Possible bug in Xdebug or a mismatch between
                    // the definition of what is and what is not a statement.
                    match ($class) {
                        // @codeCoverageIgnoreEnd
                        InvocationEvent::class => 'invoke',
                        StageEvent::class => 'stage',
                        RollbackEvent::class => 'rollback',
                        default => strtolower(
                            (string)preg_replace(
                                '/^.+\\\\(?P<name>[^\\\\]+)$/',
                                '$1',
                                $class
                            )
                        )
                    },
                    $event instanceof Stringable ? (string)$event : $class
                )
            );
        }
    }

    public function onInvoke(InvocationEvent $event): void
    {
        $this->logger->info("[invoke] {$event->invocation}");
    }

    public function onAfterInvoke(InvocationResultEvent $event): void
    {
        $result = $event->result;

        if ($result->exception) {
            $this->logger->error(
                $result->exception->getMessage()
            );
        }

        if (!$result->invoked) {
            $this->logger->debug("[invoke] Skipped: {$result}");
            return;
        }

        if ($result->success) {
            $this->logger->info("[invoke] Success: {$result}");
            return;
        }

        $this->logger->error("[invoke] Failed: {$result}");
    }

    public function onStage(StageEvent $event): void
    {
        $this->logger->debug("[stage] Staging: {$event->stage}");
    }

    public function onAfterStage(StageResultEvent $event): void
    {
        if ($event->result->staged) {
            $this->logger->info("[stage] Staged: {$event->result}");
            return;
        }

        if ($event->result->requiresInvoke) {
            $this->logger->warning("[stage] Not staged: {$event->result}");
            return;
        }

        $this->logger->debug("[stage] Not required: {$event->result}");
    }

    public function onRollback(RollbackEvent $event): void
    {
        $this->logger->warning("[rollback] Rolling back: {$event->rollback}");

        if ($event->reason !== null) {
            $this->logger->debug($event->reason->getMessage());
        }
    }
}
