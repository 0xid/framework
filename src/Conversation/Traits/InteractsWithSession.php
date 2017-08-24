<?php

declare(strict_types=1);

namespace FondBot\Conversation\Traits;

use FondBot\Channels\Chat;
use FondBot\Channels\User;

trait InteractsWithSession
{
    /**
     * Get chat.
     *
     * @return Chat
     */
    protected function getChat(): Chat
    {
        return session()->getChat();
    }

    /**
     * Get user.
     *
     * @return User
     */
    protected function getUser(): User
    {
        return session()->getUser();
    }
}
