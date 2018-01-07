<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BestReplyTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_reply_can_be_marked_as_best_reply()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $replies = create('App\Reply', ['thread_id' => $thread->id], 2);

        $this->assertFalse($replies[1]->isBest());

        $this->postJson(route('best-reply.store', $replies[1]));

        $this->assertTrue($replies[1]->fresh()->isBest());
    }

    /** @test */
    public function only_the_thread_creator_can_mark_reply_as_best()
    {
        $this->withExceptionHandling();
        $this->signIn();
        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $this->assertFalse($reply->isbest());
        $this->signIn(create('App\User'));

        $this->postJson(route('best-reply.store', $reply))->assertStatus(403);

        $this->assertFalse($reply->fresh()->isBest());
    }

    /** @test */
    public function if_a_best_reply_was_deleted_then_the_thread_should_reflect_that()
    {
        $this->signIn();
        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $reply->thread->markBestReply($reply);

        $this->deleteJson(route('reply.destroy', $reply));

        $this->assertNull($reply->thread->fresh()->best_reply_id);

    }
}
