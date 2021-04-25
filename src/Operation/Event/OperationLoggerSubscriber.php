<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Operation\Event;


use Johmanx10\Transaction\Event\DefaultPreventableInterface;
use Psr\Log\LoggerInterface;
use Stringable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OperationLoggerSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger) {}

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            DefaultPreventableInterface::class => ['onPreventable', -1],
            InvocationEvent::class => 'onInvoke',
            InvocationResultEvent::class => 'onAfterInvoke',
            RollbackEvent::class => 'onRollback',
            StageEvent::class => 'onStage',
            StageResultEvent::class => 'onAfterStage'
        ];
    }

    public function onPreventable(DefaultPreventableInterface $event): void
    {
        if ($event->isDefaultPrevented()) {
            $this->logger->debug(
                sprintf(
                    'Prevented default for subject of: %s',
                    $event instanceof Stringable
                        ? $event
                        : get_class($event)
                )
            );
        }
    }

    public function onInvoke(InvocationEvent $event): void
    {
        $this->logger->info("[invoke]\t{$event->invocation}");
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
            $this->logger->debug("[invoke]\tSkipped: {$result}");
            return;
        }

        if ($result->success) {
            $this->logger->info("[invoke]\tSuccess: {$result}");
            return;
        }

        $this->logger->error("[invoke]\tFailed: {$result}");
    }

    public function onStage(StageEvent $event): void
    {
        $this->logger->debug("[stage]\tStaging: {$event->stage}");
    }

    public function onAfterStage(StageResultEvent $event): void
    {
        if ($event->result->staged) {
            $this->logger->info("[stage]\tStaged: {$event->result}");
            return;
        }

        if ($event->result->requiresInvoke) {
            $this->logger->warning("[stage]\tNot staged: {$event->result}");
            return;
        }

        $this->logger->debug("[stage]\tNot required: {$event->result}");
    }

    public function onRollback(RollbackEvent $event): void
    {
        $this->logger->warning("[rollback]\tRolling back: {$event->rollback}");

        if ($event->reason !== null) {
            $this->logger->debug($event->reason->getMessage());
        }
    }
}
