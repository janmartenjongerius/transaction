<?php
declare(strict_types=1);

use Johmanx10\Transaction\Event\CommitResultEvent;
use Johmanx10\Transaction\Event\RollbackBlockedEvent;
use Johmanx10\Transaction\Event\RollbackResultEvent;
use Johmanx10\Transaction\Event\StagingResultEvent;
use Johmanx10\Transaction\Operation\Event\InvocationEvent;
use Johmanx10\Transaction\Operation\Event\InvocationResultEvent;
use Johmanx10\Transaction\Operation\Event\RollbackEvent;
use Johmanx10\Transaction\Operation\Event\StageEvent;
use Johmanx10\Transaction\Operation\Event\StageResultEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcher;

require_once __DIR__ . '/../../vendor/autoload.php';

return function (
    InputInterface $input,
    OutputInterface $output
): EventDispatcherInterface {
    $dispatcher = new EventDispatcher();
    $io = new SymfonyStyle($input, $output);

    if ($io->isDebug()) {
        $dispatcher->addListener(
            StageEvent::class,
            function (StageEvent $event) use ($io): void {
                $io->note("Staging: {$event->stage}");
            }
        );
    }

    if ($io->isVerbose()) {
        $dispatcher->addListener(
            StageResultEvent::class,
            function (StageResultEvent $event) use ($io): void {
                $required = $event->result->requiresInvoke
                    ? 'Required'
                    : 'Skip invoke';

                if ($event->result->staged) {
                    $io->note("Staged: {$event->result} ({$required})");

                    return;
                }

                if ($event->result->requiresInvoke) {
                    $io->warning("Not staged: {$event->result}");

                    return;
                }

                $io->note("Not required: {$event->result}");
            }
        );
    }

    $dispatcher->addListener(
        StagingResultEvent::class,
        function (StagingResultEvent $event) use ($io): void {
            if ($event->result->isStaged()) {
                $io->success('Transaction staged');

                return;
            }

            $io->warning('Transaction could not be staged.');
        }
    );

    if ($io->isDebug()) {
        $dispatcher->addListener(
            InvocationEvent::class,
            function (InvocationEvent $event) use ($io): void {
                $io->note($event->invocation);
            }
        );
    }

    $dispatcher->addListener(
        InvocationResultEvent::class,
        function (InvocationResultEvent $event) use ($io): void {
            $result = $event->result;

            if ($result->exception) {
                $io->error($result->exception->getMessage());
            }

            if (!$result->invoked) {
                if ($io->isDebug()) {
                    $io->warning("Skipped: {$result}");
                }

                return;
            }

            if ($result->success) {
                $io->success($result);

                return;
            }

            $io->error("Failed: {$result}");
        }
    );

    $dispatcher->addListener(
        RollbackEvent::class,
        function (RollbackEvent $event) use ($io): void {
            $io->warning("Rolling back: {$event->rollback}");

            if ($io->isDebug() && $event->reason !== null) {
                $io->note($event->reason->getMessage());
            }
        }
    );

    $dispatcher->addListener(
        RollbackResultEvent::class,
        function (RollbackResultEvent $event) use ($io): void {
            $io->writeln(
                sprintf(
                    'Rolled back %d operation(s)',
                    count($event->rollbacks)
                )
            );
            exit(1);
        }
    );

    $dispatcher->addListener(
        RollbackBlockedEvent::class,
        function (RollbackBlockedEvent $event) use ($io): void {
            $messages = ['Rollback was not allowed to proceed.'];

            if ($event->committed) {
                $messages[] = '* The transaction committed successfully.';
            }

            if ($event->rolledBack) {
                $messages[] = '* The transaction rolled back or attempted as much before.';
            }

            $io->warning($messages);
        }
    );

    $dispatcher->addListener(
        CommitResultEvent::class,
        function (CommitResultEvent $event) use ($io): void {
            $exception = $event->result->getReason();

            if ($exception !== null) {
                $io->error($exception);
            }

            if ($event->result->committed()) {
                $io->success('Transaction committed');

                return;
            }

            $io->error('Transaction not committed');
        }
    );

    return $dispatcher;
};
