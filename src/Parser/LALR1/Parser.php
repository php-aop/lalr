<?php

declare(strict_types=1);

namespace Aop\LALR\Parser\LALR1;

use Aop\LALR\Contract\AnalyzerCacheInterface;
use Aop\LALR\Contract\GrammarInterface;
use Aop\LALR\Contract\ParserInterface;
use Aop\LALR\Exception\UnexpectedTokenException;
use Aop\LALR\Contract\TokenStreamInterface;
use Aop\LALR\Parser\LALR1\Analysis\Analyzer;

/**
 * LALR(1) parser implementation.
 */
final class Parser implements ParserInterface
{
    /**
     * @var \Aop\LALR\Contract\GrammarInterface
     */
    private $grammar;

    /**
     * @var \Aop\LALR\Contract\AnalysisResultInterface
     */
    private $analysisResult;

    /**
     * Constructor.
     *
     * @param \Aop\LALR\Contract\GrammarInterface $grammar The grammar.
     */
    public function __construct(GrammarInterface $grammar, AnalyzerCacheInterface $cache = null)
    {
        $this->grammar = $grammar;

        if (null === $cache) {
            $this->analysisResult = Analyzer::getInstance()->analyze($grammar);

            return;
        }

        if ($cache->has($grammar)) {
            $this->analysisResult = $cache->get($grammar);

            return;
        }

        $this->analysisResult = Analyzer::getInstance()->analyze($grammar);
        $cache->set($grammar, $this->analysisResult);
    }

    /**
     * {@inheritdoc}
     */
    public function parse(TokenStreamInterface $stream)
    {
        $currentState = 0;
        $stateStack   = [$currentState];
        $args         = [];
        $table        = $this->analysisResult->getParseTable();

        /**
         * @var \Aop\LALR\Contract\TokenInterface $token
         */
        foreach ($stream as $token) {

            while (true) {

                $type = $token->getType();

                if (!isset($table['action'][$currentState][$type])) {
                    throw new UnexpectedTokenException($token, array_keys($table['action'][$currentState]));
                }

                $action = $table['action'][$currentState][$type];

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
                    $currentState = $table['goto'][$state][$rule->getName()];
                    $stateStack[] = $currentState;

                    continue;
                }

                return $args[0];
            }
        }
    }
}
