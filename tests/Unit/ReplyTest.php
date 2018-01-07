<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReplyTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    public function it_has_an_owner()
    {
        $reply = create('App\Reply');

        $this->assertInstanceOf('App\User', $reply->owner);
    }

    /** @test */
    public function it_knows_if_it_was_just_posted()
    {
        $reply = create('App\Reply');

        $this->assertTrue($reply->wasJustPosted());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasJustPosted());
    }

    /** @test */
    public function it_can_return_mention_users_in_the_body()
    {
        $reply = create('App\Reply', [
            'body' => '@johnDoe and @janeDow'
        ]);
        $this->assertEquals(['johnDoe', 'janeDow'], $reply->getMentionedUsers());
    }

    /** @test */
    public function it_wraps_mentioned_users_in_its_body_with_anchor_tags()
    {
        $reply = create('App\Reply', [
            'body' => '@johnDoe see this'
        ]);

        $this->assertEquals('<a href="/profiles/johnDoe">@johnDoe</a> see this'
            , $reply->body);
    }

    /** @test */
    public function it_knows_if_it_is_the_best_reply()
    {
        $reply = create('App\Reply');

        $this->assertFalse($reply->isBest());

        $reply->thread->update([
            'best_reply_id' => $reply->id
        ]);

        $this->assertTrue($reply->isBest());
    }

    /** @test */
    public function a_reply_body_is_sanitized()
    {
        $reply = make('App\Reply', ['body' => "<script>alert('bad')</script><p>This is fine</p>"]);

        $this->assertEquals("<p>This is fine</p>", $reply->body);
    }

}
