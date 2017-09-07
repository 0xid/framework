<?php

declare(strict_types=1);

namespace FondBot\Channels;

use Illuminate\Support\Manager;
use FondBot\Channels\Exceptions\ChannelNotFound;
use FondBot\Contracts\Channels\Manager as ManagerContract;

/**
 * Class ChannelManager.
 *
 * @method Driver driver($driver = null)
 * @method Driver createDriver($driver)
 */
class ChannelManager extends Manager implements ManagerContract
{
    /** @var array */
    private $channels = [];

    /**
     * Register channels.
     *
     * @param array $channels
     */
    public function register(array $channels): void
    {
        $this->channels = $channels;
    }

    /**
     * Get all channels.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->channels;
    }

    /**
     * Create channel.
     *
     * @param string $name
     *
     * @return Channel
     * @throws ChannelNotFound
     */
    public function create(string $name): Channel
    {
        if (!array_has($this->channels, $name)) {
            throw new ChannelNotFound('Channel `'.$name.'` not found.');
        }

        $parameters = $this->channels[$name];

        // Create driver and initialize it with channel parameters
        $driver = $this->createDriver($parameters['driver']);
        $driver->initialize(collect($parameters)->except('driver'));

        return new Channel($name, $driver);
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): ?string
    {
        return null;
    }
}
