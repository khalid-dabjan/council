<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_knows_the_latest_reply_a_user_has_posted()
    {
        $user = create('App\User');
        $latestReply = create('App\Reply', ['user_id' => $user->id]);

        $this->assertEquals($latestReply->id, $user->latestReply->id);
    }

    /** @test */
    public function it_returns_the_correct_avatar_for_a_user()
    {
        $user = create('App\User');
        $this->assertEquals(asset('images/avatars/default.png'), $user->avatar_path);

        $user->avatar_path = 'avatars/me.jpg';
        $this->assertEquals(asset('/storage/avatars/me.jpg'), $user->avatar_path);
    }
}
