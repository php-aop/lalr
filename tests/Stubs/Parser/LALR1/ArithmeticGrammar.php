<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Stubs\Parser\LALR1;

use Aop\LALR\Contract\TokenInterface;
use Aop\LALR\Parser\AbstractGrammar;

final class ArithmeticGrammar extends AbstractGrammar
{
    public function __construct()
    {
        $this
            ->define('Expr*')
            ->is('Expr+')

            ->is()
            ->call(function() {
                return array();
            });

        $this
            ->define('Expr+')
            ->is('Expr+', ',', 'Expr')
            ->call(function ($list, $_, $argument) {
                $list[] = $argument;

                return $list;
            })

            ->is('Expr')
            ->call(function($argument) {
                return array($argument);
            });

        $this
            ->define('Function')
            ->is('Add(', 'Expr*', ')')
            ->call(function($add, $params, $_) {
                return array_sum($params);
            });

        $this
            ->define('Expr')
            ->is('Function')

            ->is('Expr', '+', 'Expr')
            ->call(function ($l, $_, $r) {
                return $l + $r;
            })

            ->is('Expr', '-', 'Expr')
            ->call(function ($l, $_, $r) {
                return $l - $r;
            })

            ->is('Expr', '*', 'Expr')
            ->call(function ($l, $_, $r) {
                return $l * $r;
            })

            ->is('Expr', '/', 'Expr')
            ->call(function ($l, $_, $r) {
                return $l / $r;
            })

            ->is('Expr', '**', 'Expr')
            ->call(function ($l, $_, $r) {
                return $l ** $r;
            })

            ->is('(', 'Expr', ')')
            ->call(function ($_0, $e) {
                return $e;
            })

            ->is('-', 'Expr')->precedence(4)
            ->call(function ($_, $e) {
                return -$e;
            })

            ->is('INT')
            ->call(function (TokenInterface $i) {
                return (int)$i->getValue();
            });

        $this->operators('+', '-')->left()->precedence(1);
        $this->operators('*', '/')->left()->precedence(2);
        $this->operators('**')->right()->precedence(3);

        $this->start('Expr');
    }
}
