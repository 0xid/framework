<?php

declare(strict_types=1);

namespace FondBot\Foundation\Commands;

use FondBot\Conversation\Intent;
use FondBot\Contracts\Conversable;
use FondBot\Events\MessageReceived;
use FondBot\Conversation\Interaction;

class Converse
{
    private $conversable;
    private $message;

    public function __construct(Conversable $conversable, MessageReceived $message)
    {
        $this->conversable = $conversable;
        $this->message = $message;
    }

    public function handle(): void
    {
        if ($this->conversable instanceof Intent) {
            $session = session();
            $session->setIntent($this->conversable);
            $session->setInteraction(null);

            kernel()->setSession($session);

            $this->conversable->handle($this->message);
        } elseif ($this->conversable instanceof Interaction) {
            $this->conversable->handle($this->message);
        } else {
            $this->conversable->handle($this->message);
        }
    }
}
