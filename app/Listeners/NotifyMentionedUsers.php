<?php

namespace App\Listeners;

use App\Events\ReplyWasAdded;
use App\Notifications\YouWereMentioned;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyMentionedUsers
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
        User::whereIn('name', $event->reply->getMentionedUsers())
            ->get()
            ->each->notify(new YouWereMentioned($event->reply));;
    }
}
