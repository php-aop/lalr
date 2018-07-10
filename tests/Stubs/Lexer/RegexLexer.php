<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Stubs\Lexer;

use Aop\LALR\Exception\RuntimeException;
use Aop\LALR\Lexer\AbstractRegexLexer;

final class RegexLexer extends AbstractRegexLexer
{
    private $operators = ['+', '-'];

    protected function getCatchablePatterns(): array
    {
        return ['[1-9][0-9]*'];
    }

    protected function getNonCatchablePatterns(): array
    {
        return ['\s+'];
    }

    protected function getType(string &$value): string
    {
        if (\is_numeric($value)) {
            $value = (int) $value;

            return 'INT';
        }

        if (\in_array($value, $this->operators, true)) {
            return $value;
        }

        throw new RuntimeException(sprintf('Invalid token "%s"', $value));
    }
}
