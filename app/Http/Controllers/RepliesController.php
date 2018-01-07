<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatPostRequest;
use App\Notifications\YouWereMentioned;
use App\Reply;
use App\Detection\Spam;
use App\Thread;
use App\User;
use Illuminate\Support\Facades\Gate;

class RepliesController extends Controller
{

    /**
     * constructor
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    /**
     * @param $channelId
     * @param Thread $thread
     * @return mixed
     */
    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(20);
    }

    /**
     *
     * @param Thread $thread
     */
    public function store($channelId, Thread $thread, CreatPostRequest $form)
    {
        if ($thread->locked) {
            return response('The thread is locked.', 422);
        }
        $reply = $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ]);
        return $reply->load('owner');
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);
        $this->validate(request(), [
            'body' => 'required|spamfree'
        ]);
        $reply->update(request(['body']));
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'deleted']);
        }
        return back();
    }

}
