<?php

declare(strict_types=1);

namespace Aop\LALR\Utils;

/**
 * A string writer.
 */
final class StringBuffer
{
    /**
     * @var int
     */
    private $indent = 0;

    /**
     * @var string
     */
    private $string = '';

    private function __construct()
    {
        // noop
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
     * Appends the given string.
     *
     * @param mixed $anything The value to write.
     */
    public function write($anything): void
    {
        $this->string .= (string) $anything;
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
     * If a value is given, it writes it with correct indentation and a newline appended. When no string is given,
     * it adheres to the rule that empty lines should be whitespace-free (like vim) and doesn't append any indentation.
     *
     * @param mixed $anything The value to write.
     */
    public function writeln($anything = null): void
    {
        if ($anything) {

            $this->write(sprintf(
                "%s%s\n",
                str_repeat(' ', $this->indent * 4),
                (string) $anything
            ));

            return;
        }

        $this->write("\n");
    }

    /**
     * Create new instance of \Aop\LALR\Utils\StringBuffer.
     *
     * @return \Aop\LALR\Utils\StringBuffer
     */
    public static function create(): StringBuffer
    {
        return new self();
    }
}