<?php declare(strict_types=1);

namespace FondBot\Conversation\Abstracts;

use FondBot\Conversation\Context;
use FondBot\Conversation\Traits\Transitions;
use FondBot\Traits\Loggable;

abstract class Story
{

    use Loggable, Transitions;

    /** @var string */
    protected $name;

    /** @var bool */
    protected $enabled = true;

    /** @var Context */
    protected $context;

    /**
     * Story activations
     *
     * @return array
     */
    abstract public function activations(): array;

    abstract protected function start(): void;

    public function run(Context $context): void
    {
        $this->context = $context;
        $interaction = $context->getInteraction();
        $this->debug('run');

        // Process interaction from context
        if ($interaction !== null) {
            $interaction->run($context);
            return;
        }

        // Run story
        $this->start();
    }

}