<?php

declare(strict_types=1);

namespace FondBot\Tests\Unit\Drivers;

use FondBot\Drivers\Chat;
use FondBot\Drivers\User;
use FondBot\Drivers\Driver;
use FondBot\Tests\TestCase;
use FondBot\Drivers\Command;
use FondBot\Drivers\ReceivedMessage;
use Psr\Http\Message\ServerRequestInterface;
use FondBot\Drivers\Exceptions\InvalidRequest;

class DriverTest extends TestCase
{
    public function test_request_methods(): void
    {
        $request = $this->mock(ServerRequestInterface::class);
        $request->shouldReceive('getBody')->andReturnSelf()->atLeast()->once();
        $request->shouldReceive('getContents')->andReturn(json_encode(['foo' => 'bar']))->atLeast()->once();

        $driver = new DriverTestClass();

        $driver->fill([], $request);

        $this->assertSame('bar', $driver->getRequest('foo'));
        $this->assertSame(['foo' => 'bar'], $driver->getRequest());
        $this->assertTrue($driver->hasRequest('foo'));
        $this->assertFalse($driver->hasRequest('bar'));
    }

    public function test_header_methods(): void
    {
        $request = $this->mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeaders')->andReturn(['foo' => 'bar']);
        $driver = new DriverTestClass();

        $driver->fill([], $request);

        $this->assertSame(['foo' => 'bar'], $driver->getHeader());
        $this->assertSame('bar', $driver->getHeader('foo'));
        $this->assertNull($driver->getHeader('bar'));
    }

    public function test_parameters_methods(): void
    {
        $request = $this->mock(ServerRequestInterface::class);
        $driver = new DriverTestClass();

        $driver->fill(['foo' => 'bar'], $request);

        $this->assertSame('bar', $driver->getParameter('foo'));
        $this->assertNull($driver->getParameter('bar'));
    }
}

class DriverTestClass extends Driver
{
    /**
     * Configuration parameters.
     *
     * @return array
     */
    public function getConfig(): array
    {
    }

    /**
     * Verify incoming request data.
     *
     * @throws InvalidRequest
     */
    public function verifyRequest(): void
    {
    }

    /**
     * Get chat.
     *
     * @return Chat
     */
    public function getChat(): Chat
    {
    }

    /**
     * Get user.
     *
     * @return User
     */
    public function getUser(): User
    {
    }

    /**
     * Get message received from sender.
     *
     * @return ReceivedMessage
     */
    public function getMessage(): ReceivedMessage
    {
    }

    /**
     * Handle command.
     *
     * @param Command $command
     */
    public function handle(Command $command): void
    {
    }
}
