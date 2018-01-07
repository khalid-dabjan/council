<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Trending;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function show(Trending $trending)
    {
        if (request()->wantsJson()) {
            return Thread::search(request('q'))->paginate(20);
        }
        return view('threads.search', ['trending' => $trending->get()]);
    }
}