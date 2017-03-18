<?php

declare(strict_types=1);

namespace FondBot\Contracts\Conversation;

use FondBot\Contracts\Channels\Message;
use FondBot\Contracts\Channels\Receiver;
use FondBot\Conversation\Context;
use FondBot\Conversation\Keyboard;

interface Interaction
{
    /**
     * Get current context instance.
     *
     * @return Context
     */
    public function getContext(): Context;

    /**
     * Set context.
     *
     * @param Context $context
     */
    public function setContext(Context $context): void;

    /**
     * Get message receiver.
     *
     * @return Receiver
     */
    public function getReceiver(): Receiver;

    /**
     * Get sender's message.
     *
     * @return Message
     */
    public function getSenderMessage(): Message;

    /**
     * Message text to be sent to Participant.
     *
     * @return string
     */
    public function text(): string;

    /**
     * Keyboard to be shown to Participant.
     *
     * @return Keyboard|null
     */
    public function keyboard(): ?Keyboard;

    /**
     * Run interaction.
     */
    public function run(): void;
}
