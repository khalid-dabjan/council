<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipateInForumTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    public function an_authenticated_user_may_participates_in_forum_threads()
    {
        $thread = create('App\Thread');

        $reply = make('App\Reply');

        $this->signIn();

        $this->post($thread->path() . '/replies', $reply->toArray());

        $this->assertDatabaseHas('replies', ['body' => $reply->body]);

        $this->assertEquals(1, $thread->fresh()->replies_count);
    }

    /** @test */
    public function unauthenricated_users_cannot_add_replies()
    {
        $this->withExceptionHandling()
            ->post('threads/some-channel/1/replies', [])
            ->assertRedirect();
    }

    /** @test */
    public function a_reply_require_a_body()
    {
        $this->withExceptionHandling()->signIn();

        $reply = make('App\Reply', ['body' => null]);

        $threa = create('App\Thread');

        $this->post($threa->path() . '/replies', $reply->toArray())
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function unauthorized_user_cannot_delete_replies()
    {
        $this->withExceptionHandling();

        $reply = create('App\Reply');

        $this->delete('/replies/' . $reply->id)
            ->assertRedirect('/login');

        $this->signIn()
            ->delete('/replies/' . $reply->id)
            ->assertStatus(403);
    }

    /** @test */
    public function authorized_user_can_delete_replies()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->delete('/replies/' . $reply->id)
            ->assertStatus(302);

        $this->assertDatabaseMissing('replies', [
            'id' => $reply->id
        ]);

        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
    }

    /** @test */
    public function authorized_users_can_edit_replies()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $updatedReply = 'updated reply';
        $this->patch('replies/' . $reply->id, [
            'body' => $updatedReply
        ]);

        $this->assertDatabaseHas('replies', [
            'body' => $updatedReply
        ]);
    }

    /** @test */
    public function unauthorized_user_cannot_edit_replies()
    {
        $this->withExceptionHandling();

        $reply = create('App\Reply');

        $this->patch('/replies/' . $reply->id)
            ->assertRedirect('/login');

        $this->signIn()
            ->patch('/replies/' . $reply->id)
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_can_paginate_threads_replies()
    {
        $thread = create('App\Thread');
        create('App\Reply', ['thread_id' => $thread->id], 30);
        $json = $this->json('get', $thread->path() . '/replies')->json();
        $this->assertEquals(20, count($json['data']));
        $this->assertEquals(30, $json['total']);
    }

    /** @test */
    public function if_a_reply_contains_spam_it_may_not_be_created()
    {
        $this->withExceptionHandling();
        $this->signIn();
        $thread = create('App\Thread');

        $reply = make('App\Reply', [
            'body' => 'yahoo customer support'
        ]);

        $this->json('post', $thread->path() . '/replies', $reply->toArray())
            ->assertStatus(422);
    }

    /** @test */
    public function a_user_may_not_post_more_than_once_a_minute()
    {
        $this->withExceptionHandling();
        $this->signIn();
        $thread = create('App\Thread');

        $reply = make('App\Reply', [
            'body' => 'A reply'
        ]);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(200);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertStatus(422);
    }
}
