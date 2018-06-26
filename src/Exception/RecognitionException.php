<?php

namespace Aop\LALR\Exception;

class RecognitionException extends RuntimeException
{
    protected $parameter;
    protected $position;
    protected $line;

    /**
     * Constructor.
     *
     * @param string $parameter The unrecognised parameter.
     * @param int position      The character position within the current line where $parameter is located.
     * @param int $line         The line in the source where $parameter is located.
     */
    public function __construct(string $parameter, int $position, int $line)
    {
        $this->parameter = $parameter;
        $this->position  = $position;
        $this->line      = $line;

        $message = sprintf(
            'Invalid Parameter "%s" at line %d position %d.',
            $parameter,
            $line,
            $position
        );

        parent::__construct($message);
    }

    public function getParameter(): string
    {
        return $this->parameter;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getSourceLine(): int
    {
        return $this->line;
    }
}
