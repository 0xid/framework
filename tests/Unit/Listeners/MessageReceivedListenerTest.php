<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use Tests\Classes\FakeMessage;
use Tests\TestCase;
use FondBot\Contracts\Events\MessageReceived;
use FondBot\Contracts\Database\Entities\Participant;
use FondBot\Contracts\Database\Services\MessageService;

class MessageReceivedListenerTest extends TestCase
{
    public function test_full()
    {
        Participant::unguard();
        $messageService = $this->mock(MessageService::class);
        $participant = new Participant(['id' => random_int(1, time())]);
        $message = FakeMessage::create();

        $messageService->shouldReceive('create')->with([
            'sender_id' => $participant->id,
            'text' => $message->getText(),
            'location' => $message->getLocation()->toArray(),
            'attachment' => $message->getAttachment()->toArray(),
        ])->once();

        event(new MessageReceived($participant, $message));
    }

    public function test_full_without_location_and_attachment()
    {
        Participant::unguard();
        $messageService = $this->mock(MessageService::class);
        $participant = new Participant(['id' => random_int(1, time())]);
        $message = new FakeMessage($this->faker()->text());

        $messageService->shouldReceive('create')->with([
            'sender_id' => $participant->id,
            'text' => $message->getText(),
            'location' => null,
            'attachment' => null,
        ])->once();

        event(new MessageReceived($participant, $message));
    }
}
