<?php

declare(strict_types=1);

namespace Tests\Unit;

use FondBot\Kernel;
use Tests\TestCase;
use FondBot\Factory;
use FondBot\Drivers\Driver;
use FondBot\Channels\Channel;
use FondBot\Drivers\DriverManager;

class FactoryTest extends TestCase
{
    public function test_create()
    {
        $parameters = ['token' => $this->faker()->sha1];

        $this->container->bind(DriverManager::class, $driverManager = $this->mock(DriverManager::class));

        $driverManager->shouldReceive('get')
            ->with($channel = $this->mock(Channel::class))
            ->andReturn($driver = $this->mock(Driver::class))
            ->once();
        $channel->shouldReceive('getParameters')->andReturn($parameters)->once();
        $driver->shouldReceive('fill')->with($parameters, [], []);

        $factory = new Factory();

        $kernel = $factory->create($this->container, $channel, [], []);
        $this->assertInstanceOf(Kernel::class, $kernel);
    }
}
