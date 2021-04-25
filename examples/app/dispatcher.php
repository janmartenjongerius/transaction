<?php
declare(strict_types=1);

use Johmanx10\Transaction\Event\RollbackResultEvent;
use Johmanx10\Transaction\Event\TransactionLoggerSubscriber;
use Johmanx10\Transaction\Operation\Event\OperationLoggerSubscriber;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

require_once __DIR__ . '/../../vendor/autoload.php';

return function (
    LoggerInterface $logger
): EventDispatcherInterface {
    $dispatcher = new EventDispatcher();

    $dispatcher->addSubscriber(
        new TransactionLoggerSubscriber($logger)
    );
    $dispatcher->addSubscriber(
        new OperationLoggerSubscriber($logger)
    );

    $dispatcher->addListener(
        RollbackResultEvent::class,
        fn () => exit(
            defined('EXIT_ON_ERROR')
                ? EXIT_ON_ERROR
                : 1
        )
    );

    return $dispatcher;
};
