<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
//    protected $fillable = [
//        'name', 'email', 'password', 'avatar_path'
//    ];
    protected $guarded = [];

    protected $casts = [
        'confirmed' => 'boolean'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getRouteKeyName()
    {
        return 'name';
    }

    /**
     * @return HasMany
     */
    public function threads()
    {
        return $this->hasMany(Thread::class)->latest();
    }

    public function latestReply()
    {
        return $this->hasOne(Reply::class)->latest();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function confirm()
    {
        return tap($this, function ($value) {
            $value->confirmed = true;
            $value->confirmation_token = null;
            $value->save();
        });
    }

    public function isAdmin()
    {
        return in_array($this->name, ['JohnDoe', 'JaneDoe']);
    }

    public function read($thread)
    {
        $key = $this->threadReadCacheKey($thread);
        cache()->forever($key, Carbon::now());
    }

    public function threadReadCacheKey($thread)
    {
        return sprintf("user.%s.thread.%s", auth()->id(), $thread->id);
    }

    public function getAvatarPathAttribute($avatar)
    {
        return asset($avatar ? '/storage/' . $avatar : 'images/avatars/default.png');
//        return $this->avatar_path ? '/storage/' . $this->avatar_path : '/storage/avatars/default.jpg';
    }
}
