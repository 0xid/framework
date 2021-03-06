<?php

declare(strict_types=1);

namespace FondBot\Foundation;

use FondBot\Events\EventServiceProvider;
use FondBot\Toolbelt\ToolbeltServiceProvider;
use Illuminate\Support\AggregateServiceProvider;
use FondBot\Foundation\Providers\RouteServiceProvider;
use FondBot\Foundation\Providers\FoundationServiceProvider;

class ServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        FoundationServiceProvider::class,
        EventServiceProvider::class,
        ToolbeltServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
