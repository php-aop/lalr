<?php

namespace Aop\LALR\Parser\LALR1\Analysis;

use Aop\LALR\Contract\LexerInterface;
use Aop\LALR\Exception\ReduceReduceConflictException;
use Aop\LALR\Exception\ShiftReduceConflictException;
use Aop\LALR\Parser\AbstractGrammar;

use function Aop\LALR\Functions\has_diff;
use function Aop\LALR\Functions\union;


/**
 * Performs a grammar analysis and returns
 * the result.
 */
final class Analyzer
{
    /**
     * Performs a grammar analysis.
     *
     * @param \Aop\LALR\Parser\AbstractGrammar $grammar The grammar to analyse.
     *
     * @return \Aop\LALR\Parser\LALR1\Analysis\AnalysisResult The result of the analysis.
     */
    public function analyze(AbstractGrammar $grammar): AnalysisResult
    {
        $automaton = $this->buildAutomaton($grammar);
        list($parseTable, $conflicts) = $this->buildParseTable($automaton, $grammar);

        return new AnalysisResult($automaton, $parseTable, $conflicts);
    }

    /**
     * Builds the handle-finding FSA from the grammar.
     *
     * @param \Aop\LALR\Parser\AbstractGrammar $grammar The grammar.
     *
     * @return \Aop\LALR\Parser\LALR1\Analysis\Automaton The resulting automaton.
     */
    private function buildAutomaton(AbstractGrammar $grammar): Automaton
    {
        // the eventual automaton
        $automaton = new Automaton();

        // the queue of states that need processing
        $queue = new \SplQueue();

        // the BST for state kernels
        $kernelSet = new \Aop\LALR\Parser\LALR1\Analysis\TransitionsTree();

        // rules grouped by their name
        $groupedRules = $grammar->getGroupedRules();

        // FIRST sets of nonterminals
        $firstSets = $this->calculateFirstSets($groupedRules);

        // keeps a list of tokens that need to be pumped
        // through the automaton
        $pumpings = [];

        // the item from which the whole automaton
        // is derived
        $initialItem = new Item($grammar->getStartRule(), 0);

        // construct the initial state
        $state = new State($kernelSet->insert([
            [$initialItem->getRule()->getNumber(), $initialItem->getDotIndex()],
        ]), [$initialItem]);

        // the initial item automatically has EOF
        // as its lookahead
        $pumpings[] = [$initialItem, [LexerInterface::TOKEN_EOF]];

        $queue->enqueue($state);
        $automaton->addState($state);

        while (!$queue->isEmpty()) {
            $state = $queue->dequeue();

            // items of this state are grouped by
            // the active component to calculate
            // transitions easily
            $groupedItems = [];

            // calculate closure
            $added        = [];
            $currentItems = $state->getItems();
            for ($x = 0; $x < count($currentItems); $x++) {
                $item = $currentItems[$x];

                if (!$item->isReduceItem()) {
                    $component                  = $item->getActiveComponent();
                    $groupedItems[$component][] = $item;

                    // if nonterminal
                    if ($grammar->hasNonterminal($component)) {

                        // calculate lookahead
                        $lookahead = [];
                        $cs        = $item->getUnrecognizedComponents();

                        foreach ($cs as $i => $c) {
                            if (!$grammar->hasNonterminal($c)) {
                                // if terminal, add it and break the loop
                                $lookahead = union($lookahead, [$c]);

                                break;
                            } else {
                                // if nonterminal
                                $new = $firstSets[$c];

                                if (!in_array(AbstractGrammar::EPSILON, $new)) {
                                    // if the component doesn't derive
                                    // epsilon, merge FIRST sets and break
                                    $lookahead = union($lookahead, $new);

                                    break;
                                } else {
                                    // if it does

                                    if ($i < (count($cs) - 1)) {
                                        // if more components ahead, remove epsilon
                                        unset($new[array_search(AbstractGrammar::EPSILON, $new)]);
                                    }

                                    // and continue the loop
                                    $lookahead = union($lookahead, $new);
                                }
                            }
                        }

                        // two items are connected if the unrecognized
                        // part of rule 1 derives epsilon
                        $connect = false;

                        // only store the pumped tokens if there
                        // actually is an unrecognized part
                        $pump = true;

                        if (empty($lookahead)) {
                            $connect = true;
                            $pump    = false;
                        } else {
                            if (in_array(AbstractGrammar::EPSILON, $lookahead)) {
                                unset($lookahead[array_search(AbstractGrammar::EPSILON, $lookahead)]);

                                $connect = true;
                            }
                        }

                        foreach ($groupedRules[$component] as $rule) {
                            if (!in_array($component, $added)) {
                                // if $component hasn't yet been expaned,
                                // create new items for it
                                $newItem = new Item($rule, 0);

                                $currentItems[] = $newItem;
                                $state->add($newItem);

                            } else {
                                // if it was expanded, each original
                                // rule might bring new lookahead tokens,
                                // so get the rule from the current state
                                $newItem = $state->get($rule->getNumber(), 0);
                            }

                            if ($connect) {
                                $item->connect($newItem);
                            }

                            if ($pump) {
                                $pumpings[] = [$newItem, $lookahead];
                            }
                        }
                    }

                    // mark the component as processed
                    $added[] = $component;
                }
            }

            // calculate transitions
            foreach ($groupedItems as $thisComponent => $theseItems) {
                $newKernel = [];

                foreach ($theseItems as $thisItem) {
                    $newKernel[] = [
                        $thisItem->getRule()->getNumber(),
                        $thisItem->getDotIndex() + 1,
                    ];
                }

                $num = $kernelSet->insert($newKernel);

                if ($automaton->hasState($num)) {
                    // the state already exists
                    $automaton->addTransition($state->getNumber(), $thisComponent, $num);

                    // extract the connected items from the target state
                    $nextState = $automaton->getState($num);

                    foreach ($theseItems as $thisItem) {
                        $thisItem->connect(
                            $nextState->get(
                                $thisItem->getRule()->getNumber(),
                                $thisItem->getDotIndex() + 1
                            )
                        );
                    }
                } else {
                    // new state needs to be created
                    $newState = new State($num, array_map(function(Item $i) {
                        $new = new Item($i->getRule(), $i->getDotIndex() + 1);

                        // connect the two items
                        $i->connect($new);

                        return $new;
                    }, $theseItems));

                    $automaton->addState($newState);
                    $queue->enqueue($newState);

                    $automaton->addTransition($state->getNumber(), $thisComponent, $num);
                }
            }
        }

        // pump all the lookahead tokens
        foreach ($pumpings as $pumping) {
            $pumping[0]->pumpAll($pumping[1]);
        }

        return $automaton;
    }

    /**
     * Encodes the handle-finding FSA as a LR parse table.
     *
     * @param \Aop\LALR\Parser\LALR1\Analysis\Automaton $automaton
     *
     * @return array The parse table.
     */
    protected function buildParseTable(Automaton $automaton, AbstractGrammar $grammar): array
    {
        $conflictsMode = $grammar->getConflictsMode();
        $conflicts     = [];
        $errors        = [];

        // initialize the table
        $table = [
            'action' => [],
            'goto'   => [],
        ];

        foreach ($automaton->getTransitionTable() as $num => $transitions) {
            foreach ($transitions as $trigger => $destination) {
                if (!$grammar->hasNonterminal($trigger)) {
                    // terminal implies shift
                    $table['action'][$num][$trigger] = $destination;
                } else {
                    // nonterminal goes in the goto table
                    $table['goto'][$num][$trigger] = $destination;
                }
            }
        }

        foreach ($automaton->getStates() as $num => $state) {
            if (!isset($table['action'][$num])) {
                $table['action'][$num] = [];
            }

            foreach ($state->getItems() as $item) {
                if ($item->isReduceItem()) {
                    $ruleNumber = $item->getRule()->getNumber();

                    foreach ($item->getLookahead() as $token) {
                        if (isset($errors[$num]) && isset($errors[$num][$token])) {
                            // there was a previous conflict resolved as an error
                            // entry for this token.

                            continue;
                        }

                        if (array_key_exists($token, $table['action'][$num])) {
                            // conflict
                            $instruction = $table['action'][$num][$token];

                            if ($instruction > 0) {
                                if ($conflictsMode & AbstractGrammar::OPERATORS) {
                                    if ($grammar->hasOperator($token)) {
                                        $operatorInfo = $grammar->getOperator($token);

                                        $rulePrecedence = $item->getRule()->getPrecedence();

                                        // unless the rule has given precedence
                                        if ($rulePrecedence === null) {
                                            foreach (array_reverse($item->getRule()->getComponents()) as $c) {
                                                // try to extract it from the rightmost terminal
                                                if ($grammar->hasOperator($c)) {
                                                    $ruleOperatorInfo = $grammar->getOperator($c);
                                                    $rulePrecedence   = $ruleOperatorInfo->getPrecedence();

                                                    break;
                                                }
                                            }
                                        }

                                        if ($rulePrecedence !== null) {
                                            // if we actually have a rule precedence

                                            $tokenPrecedence = $operatorInfo->getPrecedence();

                                            if ($rulePrecedence > $tokenPrecedence) {
                                                // if the rule precedence is higher, reduce
                                                $table['action'][$num][$token] = -$ruleNumber;
                                            } elseif ($rulePrecedence < $tokenPrecedence) {
                                                // if the token precedence is higher, shift
                                                // (i.e. don't modify the table)
                                            } else {
                                                // precedences are equal, let's turn to associativity
                                                $assoc = $operatorInfo->getAssociativity();

                                                if ($assoc === AbstractGrammar::RIGHT) {
                                                    // if right-associative, shift
                                                    // (i.e. don't modify the table)
                                                } elseif ($assoc === AbstractGrammar::LEFT) {
                                                    // if left-associative, reduce
                                                    $table['action'][$num][$token] = -$ruleNumber;
                                                } elseif ($assoc === AbstractGrammar::NONASSOCIATIVE) {
                                                    // the token is nonassociative.
                                                    // this actually means an input error, so
                                                    // remove the shift entry from the table
                                                    // and mark this as an explicit error
                                                    // entry
                                                    unset($table['action'][$num][$token]);
                                                    $errors[$num][$token] = true;
                                                }
                                            }

                                            continue; // resolved the conflict, phew
                                        }

                                        // we couldn't calculate the precedence => the conflict was not resolved
                                        // move along.
                                    }
                                }

                                // s/r
                                if ($conflictsMode & AbstractGrammar::SHIFT) {
                                    $conflicts[] = [
                                        'state'      => $num,
                                        'lookahead'  => $token,
                                        'rule'       => $item->getRule(),
                                        'resolution' => AbstractGrammar::SHIFT,
                                    ];

                                    continue;
                                } else {
                                    throw new ShiftReduceConflictException(
                                        $num,
                                        $item->getRule(),
                                        $token,
                                        $automaton
                                    );
                                }
                            } else {
                                // r/r

                                $originalRule = $grammar->getRule(-$instruction);
                                $newRule      = $item->getRule();

                                if ($conflictsMode & AbstractGrammar::LONGER_REDUCE) {

                                    $count1 = count($originalRule->getComponents());
                                    $count2 = count($newRule->getComponents());

                                    if ($count1 > $count2) {
                                        // original rule is longer
                                        $resolvedRules = [$originalRule, $newRule];

                                        $conflicts[] = [
                                            'state'      => $num,
                                            'lookahead'  => $token,
                                            'rules'      => $resolvedRules,
                                            'resolution' => AbstractGrammar::LONGER_REDUCE,
                                        ];

                                        continue;
                                    } elseif ($count2 > $count1) {
                                        // new rule is longer
                                        $table['action'][$num][$token] = -$ruleNumber;
                                        $resolvedRules                 = [$newRule, $originalRule];

                                        $conflicts[] = [
                                            'state'      => $num,
                                            'lookahead'  => $token,
                                            'rules'      => $resolvedRules,
                                            'resolution' => AbstractGrammar::LONGER_REDUCE,
                                        ];

                                        continue;
                                    }
                                }

                                if ($conflictsMode & AbstractGrammar::EARLIER_REDUCE) {
                                    if (-$instruction < $ruleNumber) {
                                        // original rule was earlier
                                        $resolvedRules = [$originalRule, $newRule];

                                        $conflicts[] = [
                                            'state'      => $num,
                                            'lookahead'  => $token,
                                            'rules'      => $resolvedRules,
                                            'resolution' => AbstractGrammar::EARLIER_REDUCE,
                                        ];

                                        continue;
                                    } else {
                                        // new rule was earlier
                                        $table['action'][$num][$token] = -$ruleNumber;
                                        $resolvedRules                 = [$newRule, $originalRule];

                                        $conflicts[] = [
                                            'state'      => $num,
                                            'lookahead'  => $token,
                                            'rules'      => $resolvedRules,
                                            'resolution' => AbstractGrammar::EARLIER_REDUCE,
                                        ];

                                        continue;
                                    }
                                }

                                // everything failed, throw an exception
                                throw new ReduceReduceConflictException(
                                    $num,
                                    $originalRule,
                                    $newRule,
                                    $token,
                                    $automaton
                                );
                            }
                        }

                        $table['action'][$num][$token] = -$ruleNumber;
                    }
                }
            }
        }

        return [$table, $conflicts];
    }

    /**
     * Calculates the FIRST sets of all nonterminals.
     *
     * @param array $rules The rules grouped by the LHS.
     *
     * @return array Calculated FIRST sets.
     */
    protected function calculateFirstSets(array $rules): array 
    {
        // initialize
        $firstSets = [];

        foreach (array_keys($rules) as $lhs) {
            $firstSets[$lhs] = [];
        }

        do {
            $changes = false;

            foreach ($rules as $lhs => $ruleArray) {
                foreach ($ruleArray as $rule) {
                    $components = $rule->getComponents();
                    $new        = [];

                    if (empty($components)) {
                        $new = [AbstractGrammar::EPSILON];
                    } else {
                        foreach ($components as $i => $component) {
                            if (array_key_exists($component, $rules)) {
                                // if nonterminal, copy its FIRST set to
                                // this rule's first set
                                $x = $firstSets[$component];

                                if (!in_array(AbstractGrammar::EPSILON, $x)) {
                                    // if the component doesn't derive
                                    // epsilon, merge the first sets and
                                    // we're done
                                    $new = union($new, $x);

                                    break;
                                } else {
                                    // if all components derive epsilon,
                                    // the rule itself derives epsilon

                                    if ($i < (count($components) - 1)) {
                                        // more components ahead, remove epsilon
                                        unset($x[array_search(AbstractGrammar::EPSILON, $x)]);
                                    }

                                    $new = union($new, $x);
                                }
                            } else {
                                // if terminal, simply add it the the FIRST set
                                // and we're done
                                $new = union($new, [$component]);

                                break;
                            }
                        }
                    }

                    if (has_diff($new, $firstSets[$lhs])) {
                        $firstSets[$lhs] = union($firstSets[$lhs], $new);

                        $changes = true;
                    }
                }
            }
        } while ($changes);

        return $firstSets;
    }
}
