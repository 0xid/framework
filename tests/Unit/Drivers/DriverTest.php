<?php

declare(strict_types=1);

namespace Tests\Unit\Drivers;

use Tests\TestCase;
use FondBot\Drivers\User;
use FondBot\Drivers\Driver;
use FondBot\Conversation\Keyboard;
use FondBot\Drivers\OutgoingMessage;
use FondBot\Drivers\ReceivedMessage;
use FondBot\Drivers\Exceptions\InvalidRequest;

class DriverTest extends TestCase
{
    public function test_request_methods()
    {
        $driver = new DriverTestClass();

        $driver->fill([], ['foo' => 'bar']);

        $this->assertSame('bar', $driver->getRequest('foo'));
        $this->assertSame(['foo' => 'bar'], $driver->getRequest());
        $this->assertTrue($driver->hasRequest('foo'));
        $this->assertFalse($driver->hasRequest('bar'));
    }

    public function test_header_methods()
    {
        $driver = new DriverTestClass();

        $driver->fill([], [], ['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], $driver->getHeaders());
        $this->assertSame('bar', $driver->getHeader('foo'));
        $this->assertNull($driver->getHeader('bar'));
    }

    public function test_parameters_methods()
    {
        $driver = new DriverTestClass();

        $driver->fill(['foo' => 'bar']);

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
     * Send reply to participant.
     *
     * @param User          $sender
     * @param string        $text
     * @param Keyboard|null $keyboard
     *
     * @return OutgoingMessage
     */
    public function sendMessage(User $sender, string $text, Keyboard $keyboard = null): OutgoingMessage
    {
    }
}
