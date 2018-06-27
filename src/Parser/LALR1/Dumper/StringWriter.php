<?php

namespace Aop\LALR\Parser\LALR1\Dumper;

/**
 * A string writer.
 */
final class StringWriter
{
    /**
     * @var int
     */
    private $indent = 0;

    /**
     * @var string
     */
    private $string = '';

    /**
     * Appends the given string.
     *
     * @param string $string The string to write.
     */
    public function write(string $string)
    {
        $this->string .= $string;
    }

    /**
     * Gets the string as written so far.
     *
     * @return string The string.
     */
    public function get(): string
    {
        return $this->string;
    }

    /**
     * Adds a level of indentation.
     */
    public function indent(): void
    {
        $this->indent++;
    }

    /**
     * Removes a level of indentation.
     */
    public function outdent(): void
    {
        $this->indent--;
    }

    /**
     * If a string is given, it writes
     * it with correct indentation and
     * a newline appended. When no string
     * is given, it adheres to the rule
     * that empty lines should be whitespace-free
     * (like vim) and doesn't append any
     * indentation.
     *
     * @param string $string The string to write.
     */
    public function writeLine($string = null): void
    {
        if ($string) {

            $this->write(sprintf(
                "%s%s\n",
                str_repeat(' ', $this->indent * 4),
                $string
            ));

            return;
        }

        $this->write("\n");
    }
}