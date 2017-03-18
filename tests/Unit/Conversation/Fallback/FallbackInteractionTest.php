<?php

declare(strict_types=1);

namespace Tests\Unit\Conversation\Fallback;

use FondBot\Conversation\Context;
use FondBot\Conversation\Fallback\FallbackInteraction;
use FondBot\Conversation\Fallback\FallbackStory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Classes\FakeDriver;
use Tests\TestCase;

/**
 * @property FallbackInteraction interaction
 */
class FallbackInteractionTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->interaction = new FallbackInteraction;
    }

    public function test_process()
    {
        $context = new Context(new FakeDriver, new FallbackStory, $this->interaction);

        $this->interaction->setContext($context);
        $this->interaction->run();
    }
}
