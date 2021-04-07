<?php

declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;

require_once __DIR__ . '/../../vendor/autoload.php';

$inputDefinition = new InputDefinition();
$inputDefinition->addOption(
    new InputOption(
        'dry-run',
        'd',
        InputOption::VALUE_NONE,
        'Whether to dry-run the operations'
    )
);

$inputDefinition->addOption(
    new InputOption(
        'verbose',
        'v',
        InputOption::VALUE_NONE,
        'Verbosity level'
    )
);

$input = new ArgvInput(null, $inputDefinition);
$output = new ConsoleOutput(
    $input->getOption('verbose')
        ? ConsoleOutput::VERBOSITY_DEBUG
        : ConsoleOutput::VERBOSITY_NORMAL
);

return [$input, $output];
