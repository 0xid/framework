<?php

declare(strict_types=1);

namespace Tests\Unit\Conversation;

use FondBot\Bot;
use Tests\TestCase;
use FondBot\Conversation\Intent;
use FondBot\Conversation\Context;
use Tests\Classes\Fakes\FakeIntent;
use Tests\Classes\Fakes\FakeInteraction;

/**
 * @property mixed|\Mockery\Mock|\Mockery\MockInterface $bot
 * @property mixed|\Mockery\Mock|\Mockery\MockInterface $driver
 * @property mixed|\Mockery\Mock|\Mockery\MockInterface $context
 * @property Intent                                     $intent
 * @property mixed|\Mockery\Mock|\Mockery\MockInterface $interaction
 */
class IntentTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->bot = $this->mock(Bot::class);
        $this->context = $this->mock(Context::class);
        $this->bot->shouldReceive('getContext')->andReturn($this->context);

        $this->intent = new FakeIntent;
        $this->interaction = $this->mock(FakeInteraction::class);
    }

    public function test_handle()
    {
        $this->bot->shouldReceive('get')->with(FakeInteraction::class)->andReturn($this->interaction)->once();
        $this->bot->shouldReceive('converse')->with($this->interaction)->once();

        $this->intent->handle($this->bot);
    }
}
