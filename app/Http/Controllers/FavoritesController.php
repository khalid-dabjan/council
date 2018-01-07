<?php

namespace App\Http\Controllers;

use App\Reply;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(\App\Reply $reply)
    {
        $reply->favorite();
        return back();
    }

    public function destroy(Reply $reply)
    {
        $reply->unfavorite();
    }
}