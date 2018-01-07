<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Filters\ThreadsFilter;
use App\Rules\Recaptcha;
use App\Thread;
use App\Trending;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use function redirect;
use function request;
use function view;

class ThreadsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except('index', 'show');
    }

    /**
     *
     * @return View
     */
    public function index(Channel $channel, ThreadsFilter $filters, Trending $trending)
    {
        $threads = $this->getThreads($channel, $filters);
        if (request()->wantsJson()) {
            return $threads;
        }

        $trending = $trending->get();

        return view('threads.index', compact('threads', 'trending'));
    }

    /**
     *
     * @param Thread $thread
     * @return View
     */
    public function show($channelId, Thread $thread, Trending $trending)
    {
        if (auth()->check()) {
            auth()->user()->read($thread);
        }

        $trending->push($thread);

        $thread->visits()->record();
        return view('threads.show', [
            'thread' => $thread
        ]);
    }

    /**
     *
     * @param Request $request
     */
    public function store(Request $request, Recaptcha $recaptcha)
    {
        $this->validate($request, [
            'title' => 'required|spamfree',
            'body' => 'required|spamfree',
            'channel_id' => 'required|exists:channels,id',
            'g-recaptcha-response' => ['required', $recaptcha]
        ]);

        $thread = Thread::create([
            'user_id' => auth()->id(),
            'title' => $request->get('title'),
            'channel_id' => $request->get('channel_id'),
            'body' => $request->get('body')
        ]);

        if ($request->wantsJson()) {
            return response($thread, 200);
        }
        return redirect($thread->path())->with('flash', 'Thread saved');
    }


    public function create()
    {
        return view('threads.create');
    }

    public function update($channel, Thread $thread)
    {
        $this->authorize('update', $thread);
        $thread->update(request()->validate([
            'title' => 'required|spamfree',
            'body' => 'required|spamfree',
        ]));
    }

    public function destroy($channel, Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->delete();

        if (request()->wantsJson()) {
            return response([], 204);
        }

        return redirect('/threads');
    }

    protected function getThreads($channel, $filters)
    {
        $threads = Thread::latest()->filter($filters);

        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }
        return $threads->paginate(25);
    }

}
