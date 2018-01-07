<?php

namespace App;

use App\Events\ReplyWasAdded;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Laravel\Scout\Searchable;
use Stevebauman\Purify\Purify;

class Thread extends Model
{
    use RecordsActivity, Searchable;

    protected $guarded = [];
    protected $with = ['creator', 'channel'];
    protected $casts = [
        'locked' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($thread) {
            $thread->replies->each->delete();
        });

        static::created(function ($thread) {
            $thread->update(['slug' => $thread->title]);
        });
    }


    /**
     *
     * @return String
     */
    public function path()
    {
        return '/threads/' . $this->channel->slug . '/' . $this->slug;
    }

    /**
     *
     * @return type
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    /**
     *
     * @param array $reply
     * @return Reply $reply
     */
    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);
        event(new ReplyWasAdded($reply));
        return $reply;
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function scopeFilter($query, $filters)
    {
        $filters->apply($query);
    }

    public function subscribe($user_id = null)
    {
        $this->subscriptions()->create(['user_id' => $user_id ?: auth()->id()]);
        return $this;
    }

    public function unsubscribe($user_id = null)
    {
        $this->subscriptions()->where('user_id', $user_id ?: auth()->id())->delete();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscriptions::class);
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function isUpdatedFor($user)
    {
        $key = $user->threadReadCacheKey($this);
        return $this->updated_at > cache($key);
    }

    public function visits()
    {
        return new Visits($this);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function setSlugAttribute($value)
    {
        $slug = str_slug($value);
        if (static::whereSlug($slug)->exists()) {
            $slug = "{$slug}-{$this->id}";
        }
        $this->attributes['slug'] = $slug;
    }

    public function markBestReply($reply)
    {
        $this->update([
            'best_reply_id' => $reply->id
        ]);
    }

    public function toSearchableArray()
    {
        return $this->toArray() + ['path' => $this->path()];
    }

    public function getBodyAttribute($body)
    {
        return \Purify::clean($body);
    }

}
