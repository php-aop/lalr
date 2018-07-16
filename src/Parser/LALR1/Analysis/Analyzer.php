<?php

declare(strict_types=1);

namespace Aop\LALR\Parser\LALR1\Analysis;

use Aop\LALR\Cache\ArrayCache;
use Aop\LALR\Contract\CacheInterface;
use Aop\LALR\Contract\LexerInterface;
use Aop\LALR\Exception\LogicException;
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
     * @var \Aop\LALR\Contract\CacheInterface $cache
     */
    private $cache;

    public function __construct(CacheInterface $cache = null)
    {
        $this->cache = $cache ?? new ArrayCache();
    }

    /**
     * Performs a grammar analysis.
     *
     * @param \Aop\LALR\Parser\AbstractGrammar $grammar The grammar to analyse.
     *
     * @return \Aop\LALR\Parser\LALR1\Analysis\AnalysisResult The result of the analysis.
     */
    public function analyze(AbstractGrammar $grammar): AnalysisResult
    {
        if ($this->cache->has($grammar)) {
            return $this->cache->get($grammar);
        }

        $automaton                = $this->buildAutomaton($grammar);
        [$parseTable, $conflicts] = $this->buildParseTable($automaton, $grammar);
        $result                   = new AnalysisResult($automaton, $parseTable, $conflicts);

        $this->cache->set($grammar, $result);

        return $result;
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

        $automaton       = new Automaton();                                 // the eventual automaton
        $statesQueue     = new Queue();                                     // the queue of states that need processing
        $transitionsTree = new TransitionsTree();                           // the BST for state transitions
        $groupedRules    = $grammar->getGroupedRules();                     // rules grouped by their name
        $firstSets       = $this->calculateFirstSets($groupedRules);        // FIRST sets of nonterminals
        $pumpings        = [];                                              // keeps a list of tokens that need to be pumped through the automaton
        $initialItem     = new Item($grammar->getStartRule(), 0);  // the item from which the whole automaton is derived
        $initialState    = State::forItem($transitionsTree->initialize($initialItem), $initialItem); // construct the initial state
        $pumpings[]      = [$initialItem, [LexerInterface::TOKEN_EOF]];     // the initial item automatically has EOF as its lookahead

        $statesQueue->enqueue($initialState);
        $automaton->addState($initialState);

        while (!$statesQueue->isEmpty()) {

            $state = $statesQueue->dequeue();

            // items of this state are grouped by the active component to calculate transitions easily
            $groupedItems = [];

            // calculate closure
            $added             = [];
            $currentItemsQueue = new Queue($state->getItems());

            while (!$currentItemsQueue->isEmpty()) {
                $item = $currentItemsQueue->dequeue();

                if (!$item->isReduceItem()) {
                    $component                  = $item->getActiveComponent();
                    $groupedItems[$component][] = $item;

                    // if nonterminal
                    if ($grammar->hasNonterminal($component)) {

                        // calculate lookahead
                        $lookahead              = [];
                        $unrecognizedComponents = $item->getUnrecognizedComponents();

                        foreach ($unrecognizedComponents as $index => $unrecognizedComponent) {

                            if (!$grammar->hasNonterminal($unrecognizedComponent)) {
                                // if terminal, add it and break the loop
                                $lookahead = union($lookahead, [$unrecognizedComponent]);
                                break;
                            }

                            $symbol = $firstSets[$unrecognizedComponent];

                            if (!\in_array(AbstractGrammar::EPSILON, $symbol, true)) {
                                // if the component doesn't derive epsilon, merge FIRST sets and break
                                $lookahead = union($lookahead, $symbol);
                                break;
                            }

                            if ($index < (\count($unrecognizedComponents) - 1)) {
                                // if more components ahead, remove epsilon
                                unset($symbol[\array_search(AbstractGrammar::EPSILON, $symbol, true)]);
                            }

                            // and continue the loop
                            $lookahead = union($lookahead, $symbol);
                        }

                        $connect         = false; // two items are connected if the unrecognized part of rule 1 derives epsilon
                        $pump            = true;  // only store the pumped tokens if there actually is an unrecognized part
                        $shouldLookahead = 0 !== \count($lookahead);

                        if (!$shouldLookahead) {
                            $connect = true;
                            $pump    = false;
                        }

                        if ($shouldLookahead && \in_array(AbstractGrammar::EPSILON, $lookahead, true)) {
                            unset($lookahead[\array_search(AbstractGrammar::EPSILON, $lookahead, true)]);
                            $connect = true;
                        }

                        /**
                         * @var \Aop\LALR\Parser\Rule $rule
                         */
                        foreach ($groupedRules[$component] as $rule) {

                            if (!\in_array($component, $added, true)) {
                                // if component hasn't yet been expanded, create new item for it
                                $newItem = new Item($rule, 0);

                                $currentItemsQueue->enqueue($newItem);
                                $state->add($newItem);
                            } else {
                                // Component was expanded, each original rule might bring new lookahead tokens, so get the rule from the current state
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

            /**
             * Calculate transitions
             *
             * @var \Aop\LALR\Parser\LALR1\Analysis\Item[] $items
             */
            foreach ($groupedItems as $component => $items) {

                $stateNumber = $transitionsTree->insert(array_map(function(Item $item) {
                    return [$item->getRule()->getNumber(), $item->getDotIndex() + 1];
                }, $items));

                if ($automaton->hasState($stateNumber)) {

                    $automaton->addTransition($state->getNumber(), $component, $stateNumber); // the state already exists

                    $nextState = $automaton->getState($stateNumber); // extract the connected items from the target state

                    array_map(function(Item $item) use ($nextState) {
                        $nextItem = $nextState->get($item->getRule()->getNumber(), $item->getDotIndex() + 1);

                        $item->connect($nextItem);
                    }, $items);

                    continue;
                }

                $newItems = array_map(function(Item $item) {
                    $newItem = new Item($item->getRule(), $item->getDotIndex() + 1);

                    $item->connect($newItem); // connect the two items

                    return $newItem;
                }, $items);
                $newState = new State($stateNumber, $newItems); // new state needs to be created

                $automaton->addState($newState);
                $statesQueue->enqueue($newState);

                $automaton->addTransition($state->getNumber(), $component, $stateNumber);
            }
        }

        /**
         * Pump all the lookahead tokens
         *
         * @var \Aop\LALR\Parser\LALR1\Analysis\Item $item
         * @var array $symbol
         */
        foreach ($pumpings as [$item, $symbol]) {
            $item->pumpAll($symbol);
        }

        return $automaton;
    }

    /**
     * Encodes the handle-finding FSA as a LR parse table.
     *
     * @param \Aop\LALR\Parser\LALR1\Analysis\Automaton $automaton
     * @param \Aop\LALR\Parser\AbstractGrammar $grammar
     *
     * @return array The parse table.
     */
    private function buildParseTable(Automaton $automaton, AbstractGrammar $grammar): array
    {
        $conflictsMode = $grammar->getConflictsMode();
        $conflicts     = [];
        $errors        = [];

        // initialize the table
        $table = [
            'action' => [],
            'goto'   => [],
        ];

        foreach ($automaton->getTransitionTable() as $index => $transitions) {

            foreach ($transitions as $trigger => $destination) {

                if (!$grammar->hasNonterminal($trigger)) {
                    $table['action'][$index][$trigger] = $destination; // terminal implies shift
                    continue;
                }

                $table['goto'][$index][$trigger] = $destination; // nonterminal goes in the goto table
            }
        }

        foreach ($automaton->getStates() as $index => $state) {

            if (!isset($table['action'][$index])) {
                $table['action'][$index] = [];
            }

            foreach ($state->getItems() as $item) {

                if (!$item->isReduceItem()) {
                    continue;
                }

                $ruleNumber = $item->getRule()->getNumber();

                foreach ($item->getLookahead() as $token) {

                    if (isset($errors[$index][$token])) {
                        continue; // there was a previous conflict resolved as an error entry for this token.
                    }

                    if (!\array_key_exists($token, $table['action'][$index])) {
                        $table['action'][$index][$token] = -$ruleNumber;
                        continue;
                    }

                    $instruction = $table['action'][$index][$token]; // conflict

                    if ($instruction > 0) {

                        if ($conflictsMode & AbstractGrammar::OPERATORS) {

                            if ($grammar->hasOperator($token)) {

                                $operator       = $grammar->getOperator($token);
                                $rulePrecedence = $item->getRule()->getPrecedence();

                                if (null === $rulePrecedence) { // unless the rule has given precedence

                                    foreach (array_reverse($item->getRule()->getComponents()) as $component) {

                                        if ($grammar->hasOperator($component)) { // try to extract it from the rightmost terminal
                                            $rulePrecedence = $grammar->getOperator($component)->getPrecedence();
                                            break;
                                        }
                                    }
                                }


                                if ($rulePrecedence !== null) {

                                    $tokenPrecedence = $operator->getPrecedence(); // if we actually have a rule precedence

                                    if ($rulePrecedence > $tokenPrecedence) {
                                        $table['action'][$index][$token] = -$ruleNumber; // if the rule precedence is higher, reduce

                                        continue;
                                    }

                                    if ($rulePrecedence < $tokenPrecedence) {
                                        // if the token precedence is higher, shift (i.e. don't modify the table)
                                        continue;
                                    }

                                    // precedences are equal, let's turn to associativity
                                    $associativity = $operator->getAssociativity();

                                    if (AbstractGrammar::RIGHT === $associativity) {
                                        // if right-associative, shift (i.e. don't modify the table)
                                        continue;
                                    }

                                    if (AbstractGrammar::LEFT === $associativity) {
                                        // if left-associative, reduce
                                        $table['action'][$index][$token] = -$ruleNumber;

                                        continue;
                                    }

                                    if (AbstractGrammar::NONASSOCIATIVE === $associativity) {
                                        // The token is nonassociative. This actually means an input error, so remove the
                                        // shift entry from the table and mark this as an explicit error entry
                                        unset($table['action'][$index][$token]);

                                        $errors[$index][$token] = true;

                                        continue;
                                    }

                                    throw new LogicException(sprintf('Unknown associativity "%s" provided.', $associativity));
                                }
                                // we couldn't calculate the precedence => the conflict was not resolved move along.
                            }
                        }

                        if ($conflictsMode & AbstractGrammar::SHIFT) {

                            $conflicts[] = [
                                'state'      => $index,
                                'lookahead'  => $token,
                                'rule'       => $item->getRule(),
                                'resolution' => AbstractGrammar::SHIFT,
                            ];

                            continue;
                        }

                        throw new ShiftReduceConflictException(
                            $index,
                            $item->getRule(),
                            $token,
                            $automaton
                        );
                    }

                    $originalRule = $grammar->getRule(-$instruction);
                    $newRule      = $item->getRule();

                    if ($conflictsMode & AbstractGrammar::LONGER_REDUCE) {

                        $countOriginalRuleComponents = \count($originalRule->getComponents());
                        $countNewRuleComponents      = \count($newRule->getComponents());

                        if ($countOriginalRuleComponents > $countNewRuleComponents) {
                            // original rule is longer
                            $resolvedRules = [$originalRule, $newRule];

                            $conflicts[] = [
                                'state'      => $index,
                                'lookahead'  => $token,
                                'rules'      => $resolvedRules,
                                'resolution' => AbstractGrammar::LONGER_REDUCE,
                            ];

                            continue;
                        }

                        if ($countNewRuleComponents > $countOriginalRuleComponents) {
                            // new rule is longer
                            $table['action'][$index][$token] = -$ruleNumber;
                            $resolvedRules                   = [$newRule, $originalRule];

                            $conflicts[] = [
                                'state'      => $index,
                                'lookahead'  => $token,
                                'rules'      => $resolvedRules,
                                'resolution' => AbstractGrammar::LONGER_REDUCE,
                            ];

                            continue;
                        }
                    }

                    if ($conflictsMode & AbstractGrammar::EARLIER_REDUCE) {

                        if (-$instruction < $ruleNumber) { // original rule was earlier

                            $resolvedRules = [$originalRule, $newRule];

                            $conflicts[] = [
                                'state'      => $index,
                                'lookahead'  => $token,
                                'rules'      => $resolvedRules,
                                'resolution' => AbstractGrammar::EARLIER_REDUCE,
                            ];

                            continue;
                        }

                        // new rule was earlier
                        $table['action'][$index][$token] = -$ruleNumber;
                        $resolvedRules                   = [$newRule, $originalRule];

                        $conflicts[] = [
                            'state'      => $index,
                            'lookahead'  => $token,
                            'rules'      => $resolvedRules,
                            'resolution' => AbstractGrammar::EARLIER_REDUCE,
                        ];

                        continue;
                    }

                    // everything failed, throw an exception
                    throw new ReduceReduceConflictException($index, $originalRule, $newRule, $token, $automaton);
                }
            }
        }

        return [$table, $conflicts];
    }

    /**
     * Calculates the FIRST sets of all nonterminals.
     *
     * @param array $groupedRules The rules grouped by the LHS.
     *
     * @return array Calculated FIRST sets.
     */
    protected function calculateFirstSets(array $groupedRules): array
    {
        // initialize
        $firstSets = \array_combine(
            \array_keys($groupedRules), // array of indexes
            \array_fill(0, \count($groupedRules), []) // array of empty arrays
        );

        do {

            $changes = false;

            foreach ($groupedRules as $lhs => $rules) {

                /**
                 * @var \Aop\LALR\Parser\Rule $rule
                 */
                foreach ($rules as $rule) {

                    $components      = $rule->getComponents();
                    $countComponents = \count($components);
                    $symbols         = [];

                    if (0 === $countComponents) {
                        $symbols = [AbstractGrammar::EPSILON];
                    }

                    if ($countComponents > 0) {

                        foreach ($components as $index => $component) {

                            if (\array_key_exists($component, $groupedRules)) {
                                // if nonterminal, copy its FIRST set to this rule's first set
                                $ruleFirstSet = $firstSets[$component];

                                if (!\in_array(AbstractGrammar::EPSILON, $ruleFirstSet, true)) {
                                    // if the component doesn't derive epsilon, merge the first sets and we're done
                                    $symbols = union($symbols, $ruleFirstSet);
                                    break;
                                }

                                // if all components derive epsilon, the rule itself derives epsilon
                                if ($index < ($countComponents - 1)) {
                                    $epsilonPosition = \array_search(AbstractGrammar::EPSILON, $ruleFirstSet, true);
                                    // more components ahead, remove epsilon
                                    unset($ruleFirstSet[$epsilonPosition]);
                                }

                                $symbols = union($symbols, $ruleFirstSet);

                                continue;
                            }

                            // if terminal, simply add it the the FIRST set and we're done
                            $symbols = union($symbols, [$component]);

                            break;
                        }
                    }

                    if (has_diff($symbols, $firstSets[$lhs])) {
                        $firstSets[$lhs] = union($firstSets[$lhs], $symbols);
                        $changes         = true;
                    }
                }
            }

        } while ($changes);

        return $firstSets;
    }
}
