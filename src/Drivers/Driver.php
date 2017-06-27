<?php

declare(strict_types=1);

namespace FondBot\Drivers;

use RuntimeException;
use GuzzleHttp\Client;
use FondBot\Helpers\Arr;
use FondBot\Channels\Channel;
use Illuminate\Support\Collection;
use Psr\Http\Message\RequestInterface;
use FondBot\Queue\SerializableForQueue;
use Psr\Http\Message\ResponseInterface;
use FondBot\Drivers\Exceptions\InvalidRequest;
use FondBot\Contracts\Driver as DriverContract;

abstract class Driver implements DriverContract, SerializableForQueue
{
    /** @var Collection */
    protected $parameters;

    /** @var RequestInterface */
    protected $request;

    protected $guzzle;

    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * Get driver short name.
     *
     * This name is used as an alias for configuration.
     *
     * @return string
     */
    public function getShortName(): string
    {
        $shortName = explode('\\', get_class($this));

        return collect($shortName)->last();
    }

    /**
     * Initialize gateway with parameters.
     *
     * @param Channel          $channel
     * @param RequestInterface $request
     *
     * @return Driver|DriverContract|static
     */
    public function initialize(Channel $channel, RequestInterface $request): DriverContract
    {
        $this->request = $request;

        $parameters = [];

        foreach ($this->getDefaultParameters() as $key => $value) {
            $value = Arr::get($channel->getParameters(), $key, $value);

            Arr::set($parameters, $key, $value);
        }

        $this->parameters = collect($parameters);

        return $this;
    }

    /**
     * Get parameters.
     *
     * @return Collection
     */
    public function getParameters(): Collection
    {
        return $this->parameters;
    }

    /**
     * Get request body as json.
     *
     * @return array
     */
    public function getRequestJson(): array
    {
        return json_decode((string) $this->request->getBody(), true) ?? [];
    }

    /**
     * Send a GET request.
     *
     * @param string $uri
     * @param array  $options
     *
     * @return ResponseInterface
     */
    public function get(string $uri, array $options = []): ResponseInterface
    {
        return $this->guzzle->get($uri, $options);
    }

    /**
     * Send a POST request.
     *
     * @param string $uri
     * @param array  $options
     *
     * @return ResponseInterface
     */
    public function post(string $uri, array $options = []): ResponseInterface
    {
        return $this->guzzle->post($uri, $options);
    }

    /**
     * Get template compiler instance.
     *
     * @return TemplateCompiler|null
     */
    abstract public function getTemplateCompiler(): ?TemplateCompiler;

    /**
     * Get command handler instance.
     *
     * @return CommandHandler
     */
    abstract public function getCommandHandler(): CommandHandler;

    /**
     * Verify request consistency.
     *
     * @throws InvalidRequest
     */
    abstract public function verifyRequest(): void;

    /**
     * Get current chat.
     *
     * @return Chat
     */
    abstract public function getChat(): Chat;

    /**
     * Get current user.
     *
     * @return User
     */
    abstract public function getUser(): User;

    /**
     * Get message received from sender.
     *
     * @return ReceivedMessage
     */
    abstract public function getMessage(): ReceivedMessage;

    /**
     * Handle command.
     *
     * @param Command $command
     *
     * @throws RuntimeException
     */
    public function handle(Command $command): void
    {
        $this->getCommandHandler()->handle($command);
    }
}
