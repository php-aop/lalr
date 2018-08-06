<?php

declare(strict_types=1);

namespace Aop\LALR;

use Aop\LALR\Contract\ExpressionCacheInterface;
use Aop\LALR\Contract\LexerInterface;
use Aop\LALR\Contract\ParserInterface;

final class Language
{
    /**
     * @var \Aop\LALR\Contract\LexerInterface
     */
    private $lexer;

    /**
     * @var \Aop\LALR\Contract\ParserInterface
     */
    private $parser;

    /**
     * @var \Aop\LALR\Contract\ExpressionCacheInterface
     */
    private $cache;

    public function __construct(LexerInterface $lexer, ParserInterface $parser, ExpressionCacheInterface $cache)
    {
        $this->lexer  = $lexer;
        $this->parser = $parser;
        $this->cache  = $cache;
    }

    /**
     * Tokenize and parse expression.
     *
     * @param string $expression
     *
     * @return \Aop\LALR\Contract\NodeInterface|mixed
     */
    public function compile(string $expression)
    {
        if (!$this->cache->has($expression)) {
            $stream = $this->lexer->lex($expression);
            $result = $this->parser->parse($stream);

            $this->cache->set($expression, $result);

            return $result;
        }

        return $this->cache->get($expression);
    }
}
