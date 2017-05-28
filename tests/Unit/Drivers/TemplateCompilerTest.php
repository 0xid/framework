<?php

declare(strict_types=1);

namespace FondBot\Tests\Unit\Drivers;

use Mockery;
use Mockery\Mock;
use RuntimeException;
use FondBot\Tests\TestCase;
use FondBot\Templates\Keyboard;
use FondBot\Drivers\TemplateCompiler;

class TemplateCompilerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
    }

    public function test_method_exists(): void
    {
        $template = $this->mock(Keyboard::class);
        /** @var TemplateCompiler|Mock $compiler */
        $compiler = $this->mock(TemplateCompiler::class)->makePartial();

        $template->shouldReceive('getName')->andReturn('Keyboard')->once();
        $compiler->shouldReceive('compileKeyboard')->with($template)->once();

        $compiler->compile($template);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage No compile method for "foo".
     */
    public function test_method_does_not_exist(): void
    {
        $template = $this->mock(Keyboard::class);
        /** @var TemplateCompiler|Mock $compiler */
        $compiler = $this->mock(TemplateCompiler::class)->makePartial();

        $template->shouldReceive('getName')->andReturn('foo')->atLeast()->once();

        $compiler->compile($template);
    }
}
