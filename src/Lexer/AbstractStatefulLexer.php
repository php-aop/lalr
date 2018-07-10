<?php

declare(strict_types=1);

namespace Aop\LALR\Lexer;

use Aop\LALR\Exception\LogicException;
use Aop\LALR\Lexer\TokenMatcher\RegexTokenMatcher;
use Aop\LALR\Lexer\TokenMatcher\StringTokenMatcher;
use Aop\LALR\Contract\TokenInterface;

use function Aop\LALR\Functions\utf8_strlen;

/**
 * The AbstractStatefulLexer works like AbstractStatelessLexer,
 * but internally keeps notion of current lexer state.
 */
abstract class AbstractStatefulLexer extends AbstractSimpleLexer
{
    /**
     * Signifies that no action should be taken on encountering a token.
     */
    private const NO_ACTION = 0;

    /**
     * Indicates that a state should be popped of the state stack on
     * encountering a token.
     */
    private const POP_STATE = 1;

    /**
     * @var array
     */
    private $states = [];

    /**
     * @var array
     */
    private $stateStack = [];

    /**
     * @var null|string
     */
    private $stateBeingBuilt;

    /**
     * @var null|string
     */
    private $typeBeingBuilt;

    /**
     * Adds a new token definition. If given only one argument,
     * it assumes that the token type and recognized value are
     * identical.
     *
     * @param string $type  The token type.
     * @param string $value The value to be recognized.
     *
     * @return \Aop\LALR\Lexer\AbstractStatefulLexer Fluent interface.
     */
    protected function token(string $type, ?string $value = null): AbstractStatefulLexer
    {
        if ($this->stateBeingBuilt === null) {
            throw new LogicException('Define a lexer state first.');
        }

        if ($value === null) {
            $value = $type;
        }

        $this->states[$this->stateBeingBuilt]['token_matchers'][$type] = new StringTokenMatcher($value);
        $this->states[$this->stateBeingBuilt]['actions'][$type]        = self::NO_ACTION;
        $this->typeBeingBuilt                                          = $type;

        return $this;
    }

    /**
     * Adds a new regex token definition.
     *
     * @param string $type  The token type.
     * @param string $regex The regular expression used to match the token.
     *
     * @return \Aop\LALR\Lexer\AbstractStatefulLexer Fluent interface.
     */
    protected function regex(string $type, ?string $regex): AbstractStatefulLexer
    {
        if ($this->stateBeingBuilt === null) {
            throw new LogicException('Define a lexer state first.');
        }

        $this->states[$this->stateBeingBuilt]['token_matchers'][$type] = new RegexTokenMatcher($regex);
        $this->states[$this->stateBeingBuilt]['actions'][$type]        = self::NO_ACTION;
        $this->typeBeingBuilt                                          = $type;

        return $this;
    }

    /**
     * Marks the token types given as arguments to be skipped.
     *
     * @param string[] $types Unlimited number of token types.
     *
     * @return \Aop\LALR\Lexer\AbstractStatefulLexer Fluent interface.
     */
    protected function skip(string ...$types): AbstractStatefulLexer
    {
        if ($this->stateBeingBuilt === null) {
            throw new LogicException('Define a lexer state first.');
        }

        $this->states[$this->stateBeingBuilt]['skip_tokens'] = $types;

        return $this;
    }

    /**
     * Registers a new lexer state.
     *
     * @param string $state The new state name.
     *
     * @return \Aop\LALR\Lexer\AbstractStatefulLexer Fluent interface.
     */
    protected function state(string $state): AbstractStatefulLexer
    {
        $this->stateBeingBuilt = $state;
        $this->states[$state]  = [
            'token_matchers' => [],
            'actions'        => [],
            'skip_tokens'    => [],
        ];

        return $this;
    }

    /**
     * Sets the starting state for the lexer.
     *
     * @param string $state The name of the starting state.
     *
     * @return \Aop\LALR\Lexer\AbstractStatefulLexer Fluent interface.
     */
    protected function start(string $state): AbstractStatefulLexer
    {
        $this->stateStack[] = $state;

        return $this;
    }

    /**
     * Sets an action for the token type that is currently being built.
     *
     * @param mixed $action The action to take.
     *
     * @return \Aop\LALR\Lexer\AbstractStatefulLexer Fluent interface.
     */
    protected function action($action): AbstractStatefulLexer
    {
        if ($this->stateBeingBuilt === null || $this->typeBeingBuilt === null) {
            throw new LogicException('Define a lexer state and type first.');
        }

        $this->states[$this->stateBeingBuilt]['actions'][$this->typeBeingBuilt] = $action;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function shouldSkipToken(TokenInterface $token): bool
    {
        $state = $this->states[$this->stateStack[\count($this->stateStack) - 1]];

        return \in_array($token->getType(), $state['skip_tokens'], true);
    }

    /**
     * {@inheritDoc}
     */
    protected function extractToken(string $string): ?TokenInterface
    {
        if (empty($this->stateStack)) {
            throw new LogicException('You must set a starting state before lexing.');
        }

        $value  = null;
        $type   = null;
        $action = null;
        $state  = $this->states[$this->stateStack[\count($this->stateStack) - 1]];

        /**
         * @var \Aop\LALR\Contract\TokenMatcherInterface $tokenMatcher
         */
        foreach ($state['token_matchers'] as $tokenType => $tokenMatcher) {

            if (null === $string) {
                continue;
            }

            $tokenValue = null;

            if (!$tokenMatcher->match($string, $tokenValue)) {
                continue;
            }

            if ($value === null || utf8_strlen($tokenValue) > utf8_strlen($value)) {
                $value  = $tokenValue;
                $type   = $tokenType;
                $action = $state['actions'][$type];
            }
        }

        if ($type !== null) {

            if (\is_string($action)) { // enter new state
                $this->stateStack[] = $action;
            }

            if ($action === self::POP_STATE) {
                \array_pop($this->stateStack);
            }

            return new Token($type, $value, $this->getCurrentLine());
        }

        return null;
    }
}
