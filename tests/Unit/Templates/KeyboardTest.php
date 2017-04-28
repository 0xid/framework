<?php

declare(strict_types=1);

namespace Tests\Unit\Templates;

use FondBot\Tests\TestCase;
use FondBot\Templates\Keyboard;
use FondBot\Templates\Keyboard\Button;

class KeyboardTest extends TestCase
{
    public function test_create()
    {
        $buttons = [
            $this->mock(Button::class),
            $this->mock(Button::class),
        ];

        $keyboard = new Keyboard($buttons);

        $this->assertInstanceOf(Keyboard::class, $keyboard);
        $this->assertSame($buttons, $keyboard->getButtons());
        $this->assertSame(['buttons' => $buttons], $keyboard->toArray());
        $this->assertSame(json_encode(['buttons' => $buttons]), $keyboard->jsonSerialize());
    }
}
