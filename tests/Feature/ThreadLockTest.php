<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ThreadLockTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function non_admins_may_not_lock_threads()
    {
        $this->withExceptionHandling();
        $this->signIn();
        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->post(route('locked-thread.store', $thread))
            ->assertStatus(403);

        $this->assertFalse($thread->fresh()->locked);
    }

    /** @test */
    public function admins_may_lock_threads()
    {
        $this->signIn(factory('App\User')->states('admin')->create());
        $thread = create('App\Thread');

        $this->post(route('locked-thread.store', $thread));

        $this->assertTrue($thread->fresh()->locked);
    }

    /** @test */
    public function admins_may_unlock_threads()
    {
        $this->signIn(factory('App\User')->states('admin')->create());
        $thread = create('App\Thread', ['locked' => true]);

        $this->delete(route('locked-thread.destroy', $thread));

        $this->assertFalse($thread->fresh()->locked);
    }

    /** @test */
    public function once_a_thread_is_locked_it_may_not_receive_replies()
    {
        $this->signIn();

        $thread = create('App\Thread', ['locked' => true]);

        $this->post($thread->path() . '/replies', [
            'body' => 'reply body',
            'user_id' => auth()->id()
        ])->assertStatus(422);
    }
}
