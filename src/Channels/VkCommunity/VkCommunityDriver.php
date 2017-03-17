<?php

declare(strict_types=1);

namespace FondBot\Channels\VkCommunity;

use FondBot\Channels\Driver;
use FondBot\Channels\Exceptions\InvalidChannelRequest;
use FondBot\Channels\Message;
use FondBot\Channels\Receiver;
use FondBot\Channels\Sender;
use FondBot\Contracts\Channels\WebhookVerification;
use FondBot\Conversation\Keyboard;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class VkCommunityDriver extends Driver implements WebhookVerification
{
    const API_VERSION = '5.53';
    const API_URL = 'https://api.vk.com/method/';

    /**
     * @var Client
     */
    private $guzzle;

    /**
     * @var Sender
     */
    private $sender;

    /**
     * VkDriver constructor.
     *
     * @param Client $guzzle
     */
    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * Configuration parameters.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'access_token',
            'confirmation_token',
        ];
    }

    /**
     * Verify incoming request data.
     *
     * @throws InvalidChannelRequest
     */
    public function verifyRequest(): void
    {
        $type = $this->getRequest('type');
        $object = $this->getRequest('object');

        if ($type === null || $type !== 'message_new') {
            throw new InvalidChannelRequest('Invalid type');
        }

        if ($object === null) {
            throw new InvalidChannelRequest('Invalid object');
        }

        if (!isset($object['user_id'])) {
            throw new InvalidChannelRequest('Invalid user_id');
        }

        if (!isset($object['body'])) {
            throw new InvalidChannelRequest('Invalid body');
        }
    }

    /**
     * Get message sender.
     *
     * @return Sender
     * @throws \FondBot\Channels\Exceptions\InvalidChannelRequest
     */
    public function getSender(): Sender
    {
        if ($this->sender !== null) {
            return $this->sender;
        }

        try {
            $user_id = (string) $this->getRequest('object')['user_id'];
            $request = $this->guzzle->get(self::API_URL . "users.get?user_ids={$user_id}&v=" . self::API_VERSION);
            $response = json_decode($request->getBody()->getContents(), true);

            if ($response['response'][0] === null) {
                throw new InvalidChannelRequest('Invalid user data');
            }

            $this->sender = Sender::create(
                (string) $response['response'][0]['id'],
                $response['response'][0]['first_name'] . ' ' . $response['response'][0]['last_name']
            );
        } catch (RequestException $exception) {
            $this->error(get_class($exception), [$exception->getMessage()]);
        }

        return $this->sender;
    }

    /**
     * Get message received from sender.
     *
     * @return Message
     */
    public function getMessage(): Message
    {
        $text = (string) $this->getRequest('object')['body'];

        return Message::create($text);
    }

    /**
     * Send reply to participant.
     *
     * @param Receiver $receiver
     * @param string $text
     * @param Keyboard|null $keyboard
     */
    public function sendMessage(Receiver $receiver, string $text, Keyboard $keyboard = null): void
    {
        $this->guzzle->get(self::API_URL . 'messages.send?' . http_build_query([
            'message' => $text,
            'user_id' => $receiver->getIdentifier(),
            'access_token' => $this->getParameter('access_token'),
            'v' => self::API_VERSION
        ]));
    }

    /**
     * Whether current request type is verification.
     *
     * @return bool
     */
    public function isVerificationRequest(): bool
    {
        return ($this->getRequest('type') === 'confirmation');
    }

    /**
     * Run webhook verification and respond if required.
     *
     * @return mixed
     */
    public function verifyWebhook()
    {
        return $this->getParameter('confirmation_token');
    }
}
