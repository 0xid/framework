<?php

declare(strict_types=1);

namespace FondBot\Conversation\Traits;

use Illuminate\Support\Collection;
use FondBot\Conversation\Activators\Exact;
use FondBot\Conversation\Activators\InArray;
use FondBot\Conversation\Activators\Pattern;
use VerbalExpressions\PHPVerbalExpressions\VerbalExpressions;

trait Activators
{
    /**
     * Create "Exact" activator.
     *
     * @param string $value
     *
     * @return Exact
     */
    protected function exact(string $value): Exact
    {
        return new Exact($value);
    }

    /**
     * Create "Pattern" activator.
     *
     * @param string|VerbalExpressions $value
     *
     * @return Pattern
     */
    protected function pattern($value): Pattern
    {
        return new Pattern($value);
    }

    /**
     * Create "InArray" activator.
     *
     * @param array|Collection $values
     *
     * @return InArray
     */
    protected function inArray($values): InArray
    {
        return new InArray($values);
    }
}
