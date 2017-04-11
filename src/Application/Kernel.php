<?php

declare(strict_types=1);

namespace FondBot\Application;

use FondBot\Drivers\Driver;
use FondBot\Channels\Channel;
use FondBot\Contracts\Container;
use FondBot\Conversation\Intent;
use FondBot\Conversation\Context;
use FondBot\Conversation\Conversable;
use FondBot\Conversation\Interaction;
use FondBot\Conversation\IntentManager;
use FondBot\Conversation\ContextManager;
use FondBot\Drivers\Exceptions\InvalidRequest;
use FondBot\Drivers\Extensions\WebhookVerification;

class Kernel
{
    /** @var Kernel */
    protected static $instance;

    private $container;
    private $channel;
    private $driver;

    /** @var Context|null */
    private $context;

    protected function __construct(
        Container $container,
        Channel $channel,
        Driver $driver
    ) {
        $this->container = $container;
        $this->channel = $channel;
        $this->driver = $driver;
    }

    /**
     * Create new kernel instance.
     *
     * @param Container $container
     * @param Channel   $channel
     * @param Driver    $driver
     */
    public static function createInstance(
        Container $container,
        Channel $channel,
        Driver $driver
    ): void {
        static::setInstance(
            new static($container, $channel, $driver)
        );
    }

    /**
     * Get current instance.
     *
     * @return Kernel
     */
    public static function getInstance(): Kernel
    {
        return static::$instance;
    }

    /**
     * Set kernel instance.
     *
     * @param Kernel $instance
     */
    public static function setInstance(Kernel $instance): void
    {
        static::$instance = $instance;
    }

    /**
     * Get current driver instance.
     *
     * @return Driver
     */
    public function getDriver(): Driver
    {
        return $this->driver;
    }

    /**
     * Get context instance.
     *
     * @return Context|null
     */
    public function getContext(): ?Context
    {
        return $this->context;
    }

    /**
     * Set context instance.
     *
     * @param Context $context
     */
    public function setContext(Context $context): void
    {
        $this->context = $context;
    }

    /**
     * Clear context.
     */
    public function clearContext(): void
    {
        if ($this->context !== null) {
            $this->contextManager()->clear($this->context);
            $this->context = null;
        }
    }

    /**
     * Resolve from container.
     *
     * @param string $class
     *
     * @return mixed
     */
    public function get(string $class)
    {
        return $this->container->make($class);
    }

    /**
     * Process webhook request.
     *
     * @return mixed
     */
    public function process()
    {
        try {
            // Driver has webhook verification
            if ($this->driver instanceof WebhookVerification && $this->driver->isVerificationRequest()) {
                return $this->driver->verifyWebhook();
            }

            // Verify request
            $this->driver->verifyRequest();

            // Resolve context
            $this->context = $this->contextManager()->resolve($this->channel->getName(), $this->driver);

            if ($this->context->getInteraction() !== null) {
                $this->converse($this->context->getInteraction());
            } else {
                $intent = $this->intentManager()->find($this->driver->getMessage());

                if ($intent !== null) {
                    $this->converse($intent);
                }
            }

            if ($this->context !== null) {
                $this->contextManager()->save($this->context);
            }

            return 'OK';
        } catch (InvalidRequest $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Start conversation.
     *
     * @param Conversable $conversable
     */
    public function converse(Conversable $conversable): void
    {
        if ($conversable instanceof Intent) {
            $this->context->setIntent($conversable);
            $this->context->setInteraction(null);
            $this->context->setValues([]);

            $conversable->handle($this);
        } elseif ($conversable instanceof Interaction) {
            $conversable->handle($this);
        }
    }

    /**
     * Get context manager.
     *
     * @return ContextManager
     */
    private function contextManager(): ContextManager
    {
        return $this->get(ContextManager::class);
    }

    /**
     * Get intent manager.
     *
     * @return IntentManager
     */
    private function intentManager(): IntentManager
    {
        return $this->get(IntentManager::class);
    }
}
