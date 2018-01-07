<?php

namespace App\Listeners;

use App\Events\ReplyWasAdded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifySubscribedUsers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ReplyWasAdded $event
     * @return void
     */
    public function handle(ReplyWasAdded $event)
    {
        $reply = $event->reply;
        $event->reply->thread->subscriptions
            ->where('user_id', '!=', $reply->user_id)
            ->each->notify($reply);
    }
}
