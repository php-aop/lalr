<?php

namespace Aop\LALR\Command;

use Aop\LALR\Contract\AutomatonDumperInterface;
use Aop\LALR\Contract\GrammarInterface;
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
        $this->addOption('location', 'l', InputOption::VALUE_REQUIRED, 'Path to file where output should be dumped.');
        $this->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Format in which automaton will be dumped. Default is Graphviz.', 'graphviz');

        $this->setHelp(<<<EOT
Analyzes the given grammar and, if successful, exports the automaton in given format.

By default, dump will be displayed into console. You can provide path to location
as option <info>--location</info> where you would like to save dump output in file. 
If file exists, it will be overwritten.

Automaton will be, by default, dumped into graphviz format. If you wish to export
it into another one, you have to provide a dumper implementation to this command 
and pass format as option <info>--format=custom-format</info>.
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $class = strtr($input->getArgument('grammar-class'), '/', '\\');
        $io    = new SymfonyStyle($input, $output);

        $io->title(sprintf('Dumping automaton for grammar "%s".', $class));

        if (!\class_exists($class)) {
            $io->error('Such class does not exists, or it can not be auto-loaded. Exiting...');

            return 1;
        }

        $grammar = new $class();

        if (!$grammar instanceof GrammarInterface) {
            $io->error(sprintf('Class "%s" is not instance of "%s", therefore, not a valid grammar. Exiting...', $class, GrammarInterface::class));

            return 1;
        }

        $format = $input->getOption('format');
        $dumper = null;

        foreach ($this->dumpers as $candidate) {

            if ($candidate->supports($format)) {
                $dumper = $candidate;
                break;
            }
        }

        if (null === $dumper) {
            $io->error(sprintf('There is no suitable dumper to dump in format "%s". Exiting...', $format));

            return 1;
        }

        try {
            $dump = $dumper->dump($grammar, $format);
        } catch (\Throwable $e) {
            $io->error(sprintf('There was an error in dumping your grammar: "%s". Exiting...', $e->getMessage()));

            if ($io->isVerbose()) {
                throw $e;
            }

            return 1;
        }

        if ($location = $input->getOption('location')) {
            $result = @file_put_contents($location, $dump);

            if (false === $result) {
                $io->error(sprintf('Unable to save dump into file "%s". Exiting...', $location));

                return 1;
            }

            $io->success(sprintf('Dump successfully saved into "%s".', $location));

            return 0;
        }

        $io->write($dump);

        return 0;
    }
}
