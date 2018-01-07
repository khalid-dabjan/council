<?php
/**
 * Created by PhpStorm.
 * User: khaliddabjan
 * Date: 8/23/17
 * Time: 3:43 PM
 */

namespace App;


use Illuminate\Support\Facades\Redis;

class Visits
{
    protected $thread;

    public function __construct($thread)
    {
        $this->thread = $thread;
    }

    public function reset()
    {
        Redis::del($this->cacheKey());
    }

    public function count()
    {
        return Redis::get($this->cacheKey()) ?? 0;
    }

    public function cacheKey()
    {
        return $this->thread->id . "visits";
    }

    public function record()
    {
        Redis::incrby($this->cacheKey(), 1);
    }
}