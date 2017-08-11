<?php

declare(strict_types=1);

namespace FondBot\Tests\Unit\Conversation\Traits;

use FondBot\Drivers\Chat;
use FondBot\Drivers\User;
use FondBot\Tests\TestCase;
use FondBot\Channels\Channel;
use FondBot\Conversation\Context;
use FondBot\Conversation\Traits\InteractsWithContext;

class InteractsWithContextTest extends TestCase
{
    use InteractsWithContext;

    public function testContext(): void
    {
        $context = new Context(
            $this->mock(Channel::class),
            $this->mock(Chat::class),
            $this->mock(User::class),
            ['foo' => 'bar']
        );

        $this->setContext($context);

        $this->assertSame('bar', $this->context('foo'));
        $this->assertNull($this->context('bar'));
        $this->assertSame('foo', $this->context('bar', 'foo'));
        $this->assertInstanceOf(Context::class, $this->context());
    }

    public function testRemember(): void
    {
        $context = new Context(
            $this->mock(Channel::class),
            $this->mock(Chat::class),
            $this->mock(User::class),
            ['foo' => 'bar']
        );

        $this->setContext($context);

        $this->remember('some', 'value');

        $this->assertSame('value', $this->context('some'));
    }
}
