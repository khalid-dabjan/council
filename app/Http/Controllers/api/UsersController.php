<?php

namespace App\Http\Controllers\api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public function index()
    {
        $name = request('name');
        return User::where('name','LIKE',"$name%")
            ->take(5)
            ->pluck('name');
    }
}
