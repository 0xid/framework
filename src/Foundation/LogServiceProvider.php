<?php

declare(strict_types=1);

namespace FondBot\Foundation;

use Monolog\Logger;
use Monolog\ErrorHandler;
use Psr\Log\LoggerInterface;
use Monolog\Handler\HandlerInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

abstract class LogServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    protected $provides = [
        LoggerInterface::class,
    ];

    /**
     * Define handlers.
     *
     * @return HandlerInterface[]
     */
    abstract public function handlers(): array;

    /**
     * Method will be invoked on registration of a service provider implementing
     * this interface. Provides ability for eager loading of Service Providers.
     *
     * @return void
     *
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function boot(): void
    {
        $this->container->share(LoggerInterface::class, function () {
            $logger = new Logger('FondBot');

            foreach ($this->handlers() as $handler) {
                $logger->pushHandler($handler);
            }

            ErrorHandler::register($logger);

            return $logger;
        });

        $this->container->share(Logger::class, $this->container->get(LoggerInterface::class));
    }

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register(): void
    {
    }
}
