<?php

namespace JTD420\PGP\Events;

use App\Models\User;

class UserCreatedEvent
{
    public $user;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
