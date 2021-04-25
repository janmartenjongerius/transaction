<?php
declare(strict_types=1);

namespace Johmanx10\Transaction\Event;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TransactionLoggerSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger) {}

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CommitResultEvent::class => 'onAfterCommit',
            RollbackBlockedEvent::class => 'onRollbackBlocked',
            RollbackResultEvent::class => 'onAfterRollback',
            StagingResultEvent::class => 'onAfterStaging'
        ];
    }

    public function onRollbackBlocked(RollbackBlockedEvent $event): void
    {
        $this->logger->warning(
            'Rollback was not allowed to proceed.',
            [
                'committed' => $event->committed,
                'rolledBack' => $event->rolledBack
            ]
        );

        if ($event->committed) {
            $this->logger->debug('* Transaction successfully committed');
        }

        if ($event->rolledBack) {
            $this->logger->debug('* Transaction previously rolled back');
        }
    }

    public function onAfterCommit(CommitResultEvent $event): void
    {
        $exception = $event->result->getReason();

        if ($exception !== null) {
            $this->logger->error($exception);
        }

        if ($event->result->committed()) {
            $this->logger->info('Transaction committed');
            return;
        }

        $this->logger->error('Transaction not committed');
    }

    public function onAfterRollback(RollbackResultEvent $event): void
    {
        $this->logger->info(
            sprintf(
                'Performed %d rollback(s)',
                count($event->rollbacks)
            )
        );
    }

    public function onAfterStaging(StagingResultEvent $event): void
    {
        if ($event->result->isStaged()) {
            $this->logger->info('Transaction staged');
            return;
        }

        $this->logger->warning('Transaction could not be staged.');
    }
}
