--TEST--
Make sure that customizations of default preventable events will be logged.
--EXPECT--
[debug] [defaultpreventableinterface] Prevented: Johmanx10\Transaction\Event\DefaultPreventableInterface
--FILE--
<?php

declare(strict_types=1);

use Johmanx10\Transaction\Event\DefaultPreventable;
use Johmanx10\Transaction\Event\DefaultPreventableInterface;
use Johmanx10\Transaction\Operation\Event\OperationLoggerSubscriber;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;

require_once __DIR__ . '/../vendor/autoload.php';

$output = new ConsoleOutput(ConsoleOutput::VERBOSITY_DEBUG);
$logger = new ConsoleLogger($output);

$event = new class () implements DefaultPreventableInterface {
    use DefaultPreventable;
};
$event->preventDefault();

$subscriber = new OperationLoggerSubscriber($logger);
$subscriber->onAfterPrevent($event);
