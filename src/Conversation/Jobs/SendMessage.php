<?php

declare(strict_types=1);

namespace FondBot\Conversation\Jobs;

use FondBot\Channels\ChannelManager;
use FondBot\Contracts\Channels\Receiver;
use FondBot\Contracts\Conversation\Keyboard;
use FondBot\Contracts\Database\Entities\Channel;
use FondBot\Contracts\Database\Services\MessageService;
use FondBot\Contracts\Database\Services\ParticipantService;
use FondBot\Traits\Loggable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessage implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, Loggable;

    private $channel;
    private $receiver;
    private $text;
    private $keyboard;

    public function __construct(Channel $channel, Receiver $receiver, string $text, ?Keyboard $keyboard)
    {
        $this->channel = $channel;
        $this->receiver = $receiver;
        $this->text = $text;
        $this->keyboard = $keyboard;
    }

    public function handle(
        ChannelManager $channelManager,
        ParticipantService $participantService,
        MessageService $messageService
    ) {
        $this->debug('handle');

        $driver = $channelManager->createDriver($this->channel);

        // Send message to receiver
        $message = $driver->sendMessage(
            $this->receiver,
            $this->text,
            $this->keyboard
        );

        // Save message in database
        $participant = $participantService->findByChannelAndIdentifier(
            $this->channel,
            $this->receiver->getIdentifier()
        );

        $messageService->create([
            'receiver_id' => $participant->id,
            'text' => $message->getText(),
        ]);
    }

}