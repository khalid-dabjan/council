<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Stevebauman\Purify\Purify;

class Reply extends Model
{

    use Favoritable,
        RecordsActivity;

    /**
     *
     * @var type
     */
    protected $guarded = [];

    public $appends = ['FavoritesCount', 'isFavorited', 'isBest'];

    protected static function boot()
    {
        parent::boot();
        static::created(function ($reply) {
            $reply->thread->increment('replies_count');
        });
        static::deleted(function ($reply) {
            $reply->thread->decrement('replies_count');
        });
    }


    /**
     *
     * @var Array
     */
    protected $with = ['owner', 'favorites'];

    /**
     *
     * @return type
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function path()
    {
        return $this->thread->path() . "#reply-{$this->id}";
    }

    public function wasJustPosted()
    {
        return $this->created_at->gt(Carbon::now()->subMinute());
    }

    public function getMentionedUsers()
    {
        preg_match_all('/@([\w\-]+)/', $this->body, $matches);
        return $matches[1];
    }

    public function setBodyAttribute($body)
    {
        $this->attributes['body'] = preg_replace('/@([\w\-]+)/', '<a href="/profiles/$1">$0</a>', $body);
    }

    public function isBest()
    {
        return $this->thread->best_reply_id == $this->id;
    }

    public function getIsBestAttribute()
    {
        return $this->isBest();
    }

    public function getBodyAttribute($body)
    {
        return \Purify::clean($body);
    }

}
