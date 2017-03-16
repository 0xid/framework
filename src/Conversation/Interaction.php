<?php

declare(strict_types=1);

namespace FondBot\Conversation;

use FondBot\Channels\Receiver;
use FondBot\Contracts\Conversation\Interaction as InteractionContract;
use FondBot\Contracts\Events\MessageSent;
use FondBot\Conversation\Traits\Transitions;
use Illuminate\Contracts\Events\Dispatcher;

abstract class Interaction implements InteractionContract
{
    use Transitions;

    /** @var Context */
    private $context;

    /**
     * Set context.
     *
     * @param Context $context
     */
    public function setContext(Context $context): void
    {
        $this->context = $context;
    }

    /**
     * Get current context instance.
     *
     * @return Context
     */
    public function getContext(): Context
    {
        return $this->context;
    }

    public function getReceiver(): Receiver
    {
        $sender = $this->getContext()->getDriver()->getSender();

        return Receiver::create($sender->getIdentifier(), $sender->getName(), $sender->getUsername());
    }

    /**
     * Process reply.
     */
    abstract protected function process(): void;

    /**
     * Run interaction.
     */
    public function run(): void
    {
        // Perform actions before running interaction
        $this->before();

        // Process reply if current interaction in context
        // Reply to participant if not
        if ($this->context->getInteraction() instanceof $this) {
            $this->process();
        } else {
            // Update context information
            $this->context->setInteraction($this);
            $this->getContextManager()->save($this->context);

            // Send message to participant
            $this->context->getDriver()->sendMessage(
                $this->getReceiver(),
                $this->text(),
                $this->keyboard()
            );

            // Fire event that message was sent
            $this->getEventDispatcher()->dispatch(
                new MessageSent(
                    $this->context,
                    $this->getReceiver(),
                    $this->text()
                )
            );
        }

        // Perform actions before running interaction
        $this->after();
    }

    /**
     * Do something before running Interaction.
     */
    protected function before(): void
    {
    }

    /**
     * Do something after running Interaction.
     */
    protected function after(): void
    {
    }

    private function getContextManager(): ContextManager
    {
        return resolve(ContextManager::class);
    }

    private function getEventDispatcher(): Dispatcher
    {
        return resolve(Dispatcher::class);
    }
}
