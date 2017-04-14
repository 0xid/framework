<?php

declare(strict_types=1);

namespace FondBot\Tests\Unit\Channels;

use FondBot\Tests\TestCase;
use FondBot\Channels\Channel;
use FondBot\Channels\ChannelManager;

class ChannelManagerTest extends TestCase
{
    public function test_create()
    {
        $name = 'fake';
        $parameters = [
            'driver' => 'fake',
            'token' => $this->faker()->sha1,
        ];

        $manager = new ChannelManager();

        $manager->add($name, $parameters);

        $result = $manager->create($name);

        $this->assertInstanceOf(Channel::class, $result);
        $this->assertSame($name, $result->getName());
        $this->assertSame(collect($parameters)->except('driver')->toArray(), $result->getParameters());
    }

    /**
     * @expectedException \FondBot\Channels\ChannelNotFound
     * @expectedExceptionMessage Channel `fake` not found.
     */
    public function test_create_exception()
    {
        $manager = new ChannelManager();

        $manager->create('fake');
    }
}
