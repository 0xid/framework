<?php

declare(strict_types=1);

namespace FondBot\Conversation\Activators;

use FondBot\Events\MessageReceived;

class Contains implements Activator
{
    private $needles;

    /**
     * @param array|string $needles
     */
    public function __construct($needles)
    {
        $this->needles = $needles;
    }

    /**
     * Result of matching activator.
     *
     * @param MessageReceived $message
     *
     * @return bool
     */
    public function matches(MessageReceived $message): bool
    {
        $text = $message->getText();
        if ($text === null) {
            return false;
        }

        return str_contains($text, (array) $this->needles);
    }
}
