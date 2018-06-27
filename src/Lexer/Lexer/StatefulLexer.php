<?php

namespace Aop\LALR\Lexer\Lexer;

use Aop\LALR\Exception\LogicException;
use function Aop\LALR\Functions\utf8_strlen;
use Aop\LALR\Lexer\Recognizer\RegexRecognizer;
use Aop\LALR\Lexer\Recognizer\SimpleRecognizer;
use Aop\LALR\Lexer\Token;
use Aop\LALR\Lexer\TokenInterface;

/**
 * The StatefulLexer works like SimpleLexer,
 * but internally keeps notion of current lexer state.
 */
class StatefulLexer extends AbstractLexer
{
    /**
     * Signifies that no action should be taken on encountering a token.
     */
    public const NO_ACTION = 0;

    /**
     * Indicates that a state should be popped of the state stack on
     * encountering a token.
     */
    public const POP_STATE = 1;

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
     * @return \Aop\LALR\Lexer\Lexer\StatefulLexer This instance for fluent interface.
     */
    public function token(string $type, ?string $value = null): StatefulLexer
    {
        if ($this->stateBeingBuilt === null) {
            throw new LogicException('Define a lexer state first.');
        }

        if ($value === null) {
            $value = $type;
        }

        $this->states[$this->stateBeingBuilt]['recognizers'][$type] = new SimpleRecognizer($value);
        $this->states[$this->stateBeingBuilt]['actions'][$type]     = self::NO_ACTION;
        $this->typeBeingBuilt                                       = $type;

        return $this;
    }

    /**
     * Adds a new regex token definition.
     *
     * @param string $type  The token type.
     * @param string $regex The regular expression used to match the token.
     *
     * @return \Aop\LALR\Lexer\Lexer\StatefulLexer This instance for fluent interface.
     */
    public function regex(string $type, ?string $regex): StatefulLexer
    {
        if ($this->stateBeingBuilt === null) {
            throw new LogicException('Define a lexer state first.');
        }

        $this->states[$this->stateBeingBuilt]['recognizers'][$type] = new RegexRecognizer($regex);
        $this->states[$this->stateBeingBuilt]['actions'][$type]     = self::NO_ACTION;
        $this->typeBeingBuilt                                       = $type;

        return $this;
    }

    /**
     * Marks the token types given as arguments to be skipped.
     *
     * @param mixed $type,... Unlimited number of token types.
     *
     * @return \Aop\LALR\Lexer\Lexer\StatefulLexer This instance for fluent interface.
     */
    public function skip(): StatefulLexer
    {
        if ($this->stateBeingBuilt === null) {
            throw new LogicException('Define a lexer state first.');
        }

        $this->states[$this->stateBeingBuilt]['skip_tokens'] = func_get_args();

        return $this;
    }

    /**
     * Registers a new lexer state.
     *
     * @param string $state The new state name.
     *
     * @return \Aop\LALR\Lexer\Lexer\StatefulLexer This instance for fluent interface.
     */
    public function state($state): StatefulLexer
    {
        $this->stateBeingBuilt = $state;
        $this->states[$state]  = [
            'recognizers' => [],
            'actions'     => [],
            'skip_tokens' => [],
        ];

        return $this;
    }

    /**
     * Sets the starting state for the lexer.
     *
     * @param string $state The name of the starting state.
     *
     * @return \Aop\LALR\Lexer\Lexer\StatefulLexer This instance for fluent interface.
     */
    public function start(string $state): StatefulLexer
    {
        $this->stateStack[] = $state;

        return $this;
    }

    /**
     * Sets an action for the token type that is currently being built.
     *
     * @param mixed $action The action to take.
     *
     * @return \Aop\LALR\Lexer\Lexer\StatefulLexer This instance for fluent interface.
     */
    public function action($action): StatefulLexer
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
        $state = $this->states[$this->stateStack[count($this->stateStack) - 1]];

        return \in_array($token->getType(), $state['skip_tokens']);
    }

    /**
     * {@inheritDoc}
     */
    protected function extractToken(string $string): ?TokenInterface
    {
        if (empty($this->stateStack)) {
            throw new LogicException("You must set a starting state before lexing.");
        }

        $value = $type = $action = null;
        $state = $this->states[$this->stateStack[count($this->stateStack) - 1]];

        /**
         * @var \Aop\LALR\Lexer\RecognizerInterface $recognizer
         */
        foreach ($state['recognizers'] as $t => $recognizer) {

            if (null === $string) {
                continue;
            }

            $v = null;

            if ($recognizer->match($string, $v)) {

                if ($value === null || utf8_strlen($v) > utf8_strlen($value)) {
                    $value  = $v;
                    $type   = $t;
                    $action = $state['actions'][$type];
                }
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
