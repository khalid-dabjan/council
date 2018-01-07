<?php

namespace App\Policies;

use App\Reply;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RepliesPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Reply $reply)
    {
        return $reply->user_id == $user->id;
    }

    public function create(User $user)
    {
        $latestReply = $user->fresh()->latestReply;
        return !$latestReply ? true: !$latestReply->wasJustPosted();
    }
}
