<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use FondBot\Channels\Driver;
use FondBot\Channels\Receiver;
use FondBot\Contracts\Database\Entities\Channel;
use FondBot\Contracts\Database\Entities\Participant;
use FondBot\Contracts\Database\Services\MessageService;
use FondBot\Contracts\Database\Services\ParticipantService;
use FondBot\Contracts\Events\MessageSent;
use FondBot\Conversation\Context;
use Tests\TestCase;

class MessageSentListenerTest extends TestCase
{

    public function test()
    {
        Participant::unguard();
        $participantService = $this->mock(ParticipantService::class);
        $messageService = $this->mock(MessageService::class);
        $context = $this->mock(Context::class);
        $driver = $this->mock(Driver::class);
        $channel = new Channel();
        $participant = new Participant(['id' => random_int(1, time())]);
        $receiver = Receiver::create($this->faker()->uuid, $this->faker()->name, $this->faker()->userName);
        $text = $this->faker()->text;

        $context->shouldReceive('getDriver')->andReturn($driver);
        $driver->shouldReceive('getChannel')->andReturn($channel);

        $participantService->shouldReceive('findByChannelAndIdentifier')
            ->with($channel, $receiver->getIdentifier())
            ->andReturn($participant)
            ->once();

        $messageService->shouldReceive('create')->with([
            'receiver_id' => $participant->id,
            'text' => $text,
            'parameters' => [],
        ])->once();

        event(new MessageSent($context, $receiver, $text));
    }

}
