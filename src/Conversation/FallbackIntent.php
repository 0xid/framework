<?php

declare(strict_types=1);

namespace FondBot\Conversation;

use FondBot\Conversation\Activators\Activator;

class FallbackIntent extends Intent
{
    /**
     * Intent activators.
     *
     * @return Activator[]
     */
    public function activators(): array
    {
        return [];
    }

    public function run(): void
    {
        $text = collect([
            'Sorry, I could not understand you.',
            'Oops, I can\'t do that 😔',
            'My developer did not teach to do that.',
        ])->random();

        $this->sendMessage($text);
    }
}
