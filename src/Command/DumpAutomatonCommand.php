<?php

namespace Aop\LALR\Command;

use Aop\LALR\Contract\AutomatonDumperInterface;
use Parser\LALR1\Dumper\GraphvizAutomatonDumper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class DumpAutomatonCommand extends Command
{
    /**
     * @var \Aop\LALR\Contract\AutomatonDumperInterface[]
     */
    private $dumpers;

    public function __construct(array $dumpers = [])
    {
        parent::__construct(null);
        $this->registerDumper(new GraphvizAutomatonDumper());

        foreach ($dumpers as $dumper) {
            $this->registerDumper($dumper);
        }
    }

    public function registerDumper(AutomatonDumperInterface $dumper)
    {
        if (null === $this->dumpers) {
            $this->dumpers = [];
        }

        $this->dumpers[] = $dumper;
    }

    protected function configure(): void
    {
        $this->setName('lalr:dump:automaton');
        $this->addArgument('grammar', InputArgument::REQUIRED, 'The grammar class.');
        $this->addOption('output-dir', 'o', InputOption::VALUE_REQUIRED, 'Overrides the default output directory.');
        $this->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Format in which automaton will be dumped. Default is Graphviz.', 'graphviz');


        $this->setHelp(<<<EOT
Analyzes the given grammar and, if successful, exports the automaton in given format.

By default, the output directory is taken to be the one in which the grammar is
defined. You can change that with the <info>--output-dir</info> option:

 <info>--output-dir=../some/other/dir</info>

Automaton will be, by default, dumped into graphviz format. If you wish to export
it into another one, you have to provide a dumper implementation to this command 
and pass format as option <info>--format=custom-format</info>.
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $class = strtr($input->getArgument('grammar-class'), '/', '\\');
        $io    = new SymfonyStyle($input, $output);

        $io->title(sprintf('Dumping automaton for grammar "%s".', $class));

        if (!\class_exists($class)) {
            $io->error('Such class does not exists, or it can not be auto-loaded. Exiting...');
            return 1;
        }

        $grammar = new $class();

        if ($dir = $input->getOption('output-dir')) {
            $cwd = rtrim(getcwd(), DIRECTORY_SEPARATOR);

            $outputDir = $cwd . DIRECTORY_SEPARATOR . $dir;
        } else {
            $refl = new \ReflectionClass($class);
            $outputDir = pathinfo($refl->getFileName(), PATHINFO_DIRNAME);
        }
    }
}
