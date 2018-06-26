<?php

namespace Aop\LALR\Console;

use Aop\LALR\Command\ExportParseTableCommand;
use Symfony\Component\Console\Application as Base;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

final class Application extends Base
{
    public function __construct($version)
    {
        parent::__construct('LALR', $version);
    }

    protected function getCommandName(InputInterface $input)
    {
        return 'dissect';
    }

    protected function getDefaultCommands()
    {
        $default   = parent::getDefaultCommands();
        $default[] = new ExportParseTableCommand();

        return $default;
    }

    public function getDefinition()
    {
        return new InputDefinition([
            new InputOption('--help', '-h', InputOption::VALUE_NONE, 'Display this help message.'),
            new InputOption('--verbose', '-v', InputOption::VALUE_NONE, 'Increase verbosity of exceptions.'),
            new InputOption('--version', '-V', InputOption::VALUE_NONE, 'Display version information.'),
        ]);
    }
}
