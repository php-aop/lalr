<?php

namespace Aop\LALR\Tests\Stubs\Arithmetic;

use Aop\LALR\Parser\Grammar as Base;

final class Grammar extends Base
{
    public function __construct()
    {
        $this('Expr*')
            ->is('Expr+')

            ->is()
            ->call(function() {
                return array();
            });

        $this('Expr+')
            ->is('Expr+', ',', 'Expr')
            ->call(function ($list, $_, $argument) {
                $list[] = $argument;

                return $list;
            })

            ->is('Expr')
            ->call(function($argument) {
                return array($argument);
            });

        $this('Function')
            ->is('Add(', 'Expr*', ')')
            ->call(function($add, $params, $_) {
                return array_sum($params);
            });

        $this('Expr')
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

            ->is('-', 'Expr')->prec(4)
            ->call(function ($_, $e) {
                return -$e;
            })

            ->is('INT')
            ->call(function ($i) {
                return (int)$i->getValue();
            });

        $this->operators('+', '-')->left()->prec(1);
        $this->operators('*', '/')->left()->prec(2);
        $this->operators('**')->right()->prec(3);

        $this->start('Expr');
    }
}