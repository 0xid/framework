<?php

declare(strict_types=1);

namespace Tests\Unit\Conversation\Activators;

use Tests\TestCase;
use FondBot\Conversation\Activators\Exact;
use FondBot\Contracts\Drivers\ReceivedMessage;

/**
 * @property mixed|\Mockery\Mock message
 */
class ExactTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->message = $this->mock(ReceivedMessage::class);
    }

    public function test_matches()
    {
        $this->message->shouldReceive('getText')->andReturn('/start');

        $activator = new Exact('/start');
        $this->assertTrue(
            $activator->matches($this->message)
        );
    }

    public function test_does_not_match()
    {
        $this->message->shouldReceive('getText')->andReturn('/stop');

        $activator = new Exact('/start');
        $this->assertFalse(
            $activator->matches($this->message)
        );
    }

    public function test_empty_message()
    {
        $this->message->shouldReceive('getText')->andReturn(null);

        $activator = new Exact('/start');
        $this->assertFalse(
            $activator->matches($this->message)
        );
    }
}
