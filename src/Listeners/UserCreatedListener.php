<?php

namespace JTD420\PGP\Listeners;

use App\Http\Controllers\PGP\PGPController;
use App\Models\PGP\Key;
use JTD420\PGP\Events\UserCreatedEvent;

class UserCreatedListener
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(UserCreatedEvent $event)
    {
        $controller = new PGPcontroller();
        $keypair = $controller->generate_keypair($event->user->name, $event->user->email, $event->user->passphrase);
        $public_key = $keypair['public_key'];
        $private_key = $keypair['private_key'];
        $key = new Key;
        $key->public_key = $public_key;
        $key->private_key = $private_key;
        $key->user_id = $event->user->id;
        $key->save();
    }
}
