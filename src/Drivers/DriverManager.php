<?php

declare(strict_types=1);

namespace FondBot\Drivers;

use FondBot\Helpers\Arr;
use FondBot\Channels\Channel;
use League\Container\Container;
use TheCodingMachine\Discovery\Asset;
use TheCodingMachine\Discovery\Discovery;
use Psr\Http\Message\ServerRequestInterface;
use FondBot\Drivers\Exceptions\DriverNotFound;
use TheCodingMachine\Discovery\ImmutableAssetType;
use FondBot\Drivers\Exceptions\InvalidConfiguration;

class DriverManager
{
    protected $container;
    protected $discovery;

    /** @var Driver[] */
    protected $drivers;

    public function __construct(Container $container, Discovery $discovery)
    {
        $this->container = $container;
        $this->discovery = $discovery;

        $this->boot();
    }

    /**
     * Boot drivers.
     */
    protected function boot(): void
    {
        /** @var ImmutableAssetType $assets */
        $assets = $this->discovery->getAssetType(Driver::class);

        foreach ($assets->getAssets() as $asset) {
            $this->drivers[$asset->getMetadata()['name']] = $this->container->get($asset->getValue());
        }
    }

    /**
     * Get driver instance.
     *
     * @param Channel                $channel
     *
     * @param ServerRequestInterface $request
     *
     * @return Driver
     * @throws DriverNotFound
     */
    public function get(Channel $channel, ServerRequestInterface $request): Driver
    {
        $driver = $this->drivers[$channel->getDriver()];

        if ($driver === null || !$driver instanceof Driver) {
            throw new DriverNotFound('Driver `'.$channel->getDriver().'` not found.');
        }

        $this->validateParameters($channel, $driver);

        $driver->fill($channel->getParameters(), $request);

        return $driver;
    }

    /**
     * Validate channel parameters with driver requirements.
     *
     * @param Channel $channel
     * @param Driver  $driver
     */
    protected function validateParameters(Channel $channel, Driver $driver): void
    {
        /** @var Asset $asset */
        $asset = collect($this->discovery->getAssetType(Driver::class)->getAssets())
            ->first(function (Asset $asset) use ($driver) {
                return hash_equals($asset->getValue(), get_class($driver));
            });

        $parameters = Arr::get($asset->getMetadata(), 'parameters', []);

        collect($parameters)->each(function (string $parameter) use ($channel) {
            if ($channel->getParameter($parameter) === null) {
                throw new InvalidConfiguration('Invalid `'.$channel->getName().'` channel configuration.');
            }
        });
    }
}
