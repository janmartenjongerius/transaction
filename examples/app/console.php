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
        '--quiet',
        '-q',
        InputOption::VALUE_NONE,
        'Do not output any message'
    )
);
$inputDefinition->addOption(
    new InputOption(
        'verbose',
        'v|vv|vvv',
        InputOption::VALUE_NONE,
        'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug'
    )
);

$input = new ArgvInput($argv, $inputDefinition);
$output = new ConsoleOutput(
    array_reduce(
        $argv,
        fn (int $verbosity, string $flag) => match ($flag) {
            '-v'                => ConsoleOutput::VERBOSITY_VERBOSE,
            '-vv'               => ConsoleOutput::VERBOSITY_VERY_VERBOSE,
            '-vvv', '--verbose' => ConsoleOutput::VERBOSITY_DEBUG,
            '-q',   '--quiet'   => ConsoleOutput::VERBOSITY_QUIET,
            default             => $verbosity
        },
        ConsoleOutput::VERBOSITY_NORMAL
    )
);

return [$input, $output];
