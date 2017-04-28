<?php

declare(strict_types=1);

namespace Tests\Unit\Templates\Keyboard;

use FondBot\Tests\TestCase;
use FondBot\Templates\Keyboard\ReplyButton;

class ReplyButtonTest extends TestCase
{
    public function test()
    {
        $label = $this->faker()->word;

        $button = new ReplyButton($label);
        $this->assertSame($label, $button->getLabel());
    }
}
