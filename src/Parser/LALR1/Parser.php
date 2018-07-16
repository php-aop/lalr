<?php

declare(strict_types=1);

namespace Aop\LALR\Parser\LALR1;

use Aop\LALR\Contract\ParserInterface;
use Aop\LALR\Exception\UnexpectedTokenException;
use Aop\LALR\Contract\TokenStreamInterface;
use Aop\LALR\Parser\AbstractGrammar;
use Aop\LALR\Parser\LALR1\Analysis\Analyzer;

/**
 * LALR(1) parser implementation.
 */
final class Parser implements ParserInterface
{
    /**
     * @var \Aop\LALR\Parser\AbstractGrammar
     */
    private $grammar;

    /**
     * @var array
     */
    private $table;

    /**
     * Constructor.
     *
     * @param \Aop\LALR\Parser\AbstractGrammar $grammar The grammar.
     * @param \Aop\LALR\Parser\LALR1\Analysis\Analyzer $analyzer
     */
    public function __construct(AbstractGrammar $grammar, Analyzer $analyzer = null)
    {
        $analyzer      = $analyzer ?? new Analyzer();
        $this->grammar = $grammar;
        $this->table   = $analyzer->analyze($grammar)->getParseTable();
    }

    /**
     * {@inheritdoc}
     */
    public function parse(TokenStreamInterface $stream)
    {
        $currentState = 0;
        $stateStack   = [$currentState];
        $args         = [];

        /**
         * @var \Aop\LALR\Contract\TokenInterface $token
         */
        foreach ($stream as $token) {

            while (true) {

                $type = $token->getType();

                if (!isset($this->table['action'][$currentState][$type])) {
                    throw new UnexpectedTokenException($token, array_keys($this->table['action'][$currentState]));
                }

                $action = $this->table['action'][$currentState][$type];

                if ($action > 0) {
                    // shift

                    $args[]       = $token;
                    $currentState = $action;
                    $stateStack[] = $currentState;

                    break;
                }

                if ($action < 0) {
                    // reduce
                    $rule     = $this->grammar->getRule(-$action);
                    $popCount = \count($rule->getComponents());

                    $newArgs = $args;

                    if ($popCount > 0) {
                        \array_splice($stateStack, -$popCount);
                        $newArgs = \array_splice($args, -$popCount);
                    }

                    $args[]       = ($callback = $rule->getCallback()) ? \call_user_func_array($callback, $newArgs) : $newArgs[0];
                    $state        = $stateStack[\count($stateStack) - 1];
                    $currentState = $this->table['goto'][$state][$rule->getName()];
                    $stateStack[] = $currentState;

                    continue;
                }

                return $args[0];
            }
        }
    }
}
