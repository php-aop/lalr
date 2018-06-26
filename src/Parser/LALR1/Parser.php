<?php

namespace Aop\LALR\Parser\LALR1;

use Aop\LALR\Exception\UnexpectedTokenException;
use Aop\LALR\Lexer\TokenStreamInterface;
use Aop\LALR\Parser\Grammar;
use Aop\LALR\Parser\ParserInterface;

final class Parser implements ParserInterface
{
    /**
     * @var \Aop\LALR\Parser\Grammar
     */
    private $grammar;

    /**
     * @var array
     */
    private $parseTable;

    /**
     * Constructor.
     *
     * @param \Aop\LALR\Parser\Grammar $grammar The grammar.
     * @param array $parseTable                 If given, the parser doesn't have to analyze the grammar.
     */
    public function __construct(Grammar $grammar, array $parseTable = null)
    {
        $this->grammar    = $grammar;
        $this->parseTable = $parseTable ?? (new Analyzer())->analyze($grammar)->getParseTable();
    }

    /**
     * {@inheritdoc}
     */
    public function parse(TokenStreamInterface $stream)
    {
        $stateStack = [$currentState = 0];
        $args       = [];

        /**
         * @var \Aop\LALR\Lexer\TokenInterface $token
         */
        foreach ($stream as $token) {

            while (true) {

                $type = $token->getType();

                if (!isset($this->parseTable['action'][$currentState][$type])) {
                    throw new UnexpectedTokenException($token, array_keys($this->parseTable['action'][$currentState]));
                }

                $action = $this->parseTable['action'][$currentState][$type];

                if ($action > 0) {
                    // shift

                    $args[]       = $token;
                    $stateStack[] = $currentState = $action;

                    break;
                }

                if ($action < 0) {
                    // reduce
                    $rule     = $this->grammar->getRule(-$action);
                    $popCount = count($rule->getComponents());

                    $newArgs = $args;

                    if ($popCount > 0) {
                        array_splice($stateStack, -$popCount);
                        $newArgs = array_splice($args, -$popCount);
                    }

                    if ($callback = $rule->getCallback()) {
                        $args[] = call_user_func_array($callback, $newArgs);
                    } else {
                        $args[] = $newArgs[0];
                    }

                    $state        = $stateStack[count($stateStack) - 1];
                    $stateStack[] = $currentState = $this->parseTable['goto'][$state][$rule->getName()];

                    continue;
                }

                return $args[0];
            }
        }
    }
}
