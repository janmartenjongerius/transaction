<?php
declare(strict_types=1);

use Johmanx10\Transaction\TransactionFactory;
use Johmanx10\Transaction\TransactionFactoryInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

require_once __DIR__ . '/../../vendor/autoload.php';

return function (
    InputInterface $input,
    OutputInterface $output
): TransactionFactoryInterface {
    return new TransactionFactory(
        (require __DIR__ . '/dispatcher.php')(new ConsoleLogger($output)),
        $input->getOption('dry-run')
            ? TransactionFactory::STRATEGY_DRY_RUN
            : TransactionFactory::STRATEGY_EXECUTE
    );
};
