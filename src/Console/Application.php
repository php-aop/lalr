<?php

namespace Aop\LALR\Console;

use Aop\LALR\Command\DumpAutomatonCommand;
use Symfony\Component\Console\Application as Base;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

final class Application extends Base
{
    public function __construct($version)
    {
        parent::__construct('LALR', $version);
    }

    protected function getDefaultCommands(): array
    {
        $defaults   = parent::getDefaultCommands();
        $defaults[] = new DumpAutomatonCommand();

        return $defaults;
    }

    public function getDefinition(): InputDefinition
    {
        return new InputDefinition([
            new InputOption('--help', '-h', InputOption::VALUE_NONE, 'Display this help message.'),
            new InputOption('--verbose', '-v', InputOption::VALUE_NONE, 'Increase verbosity of exceptions.'),
            new InputOption('--version', '-V', InputOption::VALUE_NONE, 'Display version information.'),
        ]);
    }
}
