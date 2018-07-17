<?php

declare(strict_types=1);

namespace Parser\LALR1\Dumper;

use Aop\LALR\Contract\AutomatonDumperInterface;
use Aop\LALR\Contract\AutomatonInterface;
use Aop\LALR\Parser\LALR1\Analysis\Automaton;
use Aop\LALR\Parser\LALR1\Analysis\Item;
use Aop\LALR\Parser\LALR1\Analysis\State;
use Aop\LALR\Utils\StringBuffer;

/**
 * Automaton dumper in Graphviz format.
 *
 * @see https://www.graphviz.org/documentation
 */
final class GraphvizAutomatonDumper implements AutomatonDumperInterface
{
    /**
     * Supported formats.
     */
    private const FORMAT = ['gv', 'dot', 'graphviz'];

    /**
     * {@inheritdoc}
     */
    public function supports(AutomatonInterface $automaton, string $format): bool
    {
        if (!$automaton instanceof Automaton) {
            return false;
        }

        if (!\in_array($format, self::FORMAT, true)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function dump(AutomatonInterface $automaton): string
    {
        $buffer = StringBuffer::create();

        $this->header($buffer);
        $buffer->writeln();

        foreach ($automaton->getStates() as $state) {
            $this->state($buffer, $state);
        }

        $buffer->writeln();

        foreach ($automaton->getTransitionTable() as $num => $map) {

            foreach ($map as $trigger => $destination) {

                $buffer->writeln(sprintf(
                    '%d -> %d [label="%s"];',
                    $num,
                    $destination,
                    $trigger
                ));
            }
        }

        $buffer->outdent();
        $this->footer($buffer);

        return $buffer->get();
    }

    private function header(StringBuffer $buffer, $stateNumber = null): void
    {
        $buffer->writeln(sprintf(
            'digraph %s {',
            $stateNumber ? 'State'.$stateNumber : 'Automaton'
        ));

        $buffer->indent();
        $buffer->writeln('rankdir="LR";');
    }

    private function state(StringBuffer $buffer, State $state, $full = true): void
    {
        $number = $state->getNumber();

        $buffer->write(sprintf('%d [label="State %d', $number, $number));

        if (true === $full) {
            $buffer->write('\n\n');
            $items = [];

            foreach ($state->getItems() as $item) {
                $items[] = $this->format($item);
            }

            $buffer->write(implode('\n', $items));
        }

        $buffer->writeln('"];');
    }

    private function format(Item $item): string
    {
        $rule       = $item->getRule();
        $components = $rule->getComponents();

        // the dot
        array_splice($components, $item->getDotIndex(), 0, ['&bull;']);

        if ($rule->getNumber() === 0) {
            $string = '';
        } else {
            $string = sprintf("%s &rarr; ", $rule->getName());
        }

        $string .= implode(' ', $components);

        if ($item->isReduceItem()) {
            $string .= sprintf(' [%s]', implode(' ', $item->getLookahead()));
        }

        return $string;
    }

    protected function footer(StringBuffer $buffer): void
    {
        $buffer->writeln('}');
    }
}