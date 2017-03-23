<?php

declare(strict_types=1);

namespace Tests\Unit\Conversation;

use Tests\TestCase;
use FondBot\Conversation\Story;
use FondBot\Conversation\Context;
use Tests\Classes\Fakes\FakeStory;
use FondBot\Conversation\StoryManager;
use FondBot\Contracts\Channels\SenderMessage;
use FondBot\Conversation\Fallback\FallbackStory;

/**
 * @property StoryManager manager
 */
class StoryManagerTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->manager = new StoryManager;
    }

    public function test_find_has_story_in_context()
    {
        $context = $this->mock(Context::class);
        $message = $this->mock(SenderMessage::class);
        $story = $this->mock(Story::class);

        $context->shouldReceive('getStory')->andReturn($story);

        $result = $this->manager->find($context, $message);
        $this->assertSame($story, $result);
    }

    public function test_find_fallback_story()
    {
        $this->manager->add(FakeStory::class);

        $context = $this->mock(Context::class);
        $message = $this->mock(SenderMessage::class);

        $context->shouldReceive('getStory')->andReturn(null);
        $message->shouldReceive('getText')->andReturn('/start');

        $result = $this->manager->find($context, $message);
        $this->assertInstanceOf(FallbackStory::class, $result);
    }

    public function test_find_no_story_in_context_activation_found()
    {
        $this->manager->add(FakeStory::class);

        $context = $this->mock(Context::class);
        $message = $this->mock(SenderMessage::class);

        $context->shouldReceive('getStory')->andReturn(null);
        $message->shouldReceive('getText')->andReturn('/example');

        $result = $this->manager->find($context, $message);
        $this->assertInstanceOf(FakeStory::class, $result);
    }
}
