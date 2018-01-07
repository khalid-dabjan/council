<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateThreadTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();
        $this->signIn();
        $this->withExceptionHandling();
    }

    /** @test */
    public function a_thread_may_be_updated_by_its_owner()
    {
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $this->patch($thread->path(), [
            'body' => 'changed body',
            'title' => 'changed title'
        ]);
        tap($thread->fresh(), function ($thread) {
            $this->assertEquals('changed body', $thread->body);
            $this->assertEquals('changed title', $thread->title);
        });
    }

    /** @test */
    public function a_thread_requires_title_and_body_when_updating()
    {
        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $this->patch($thread->path(), [
            'body' => 'changed',
        ])->assertSessionHasErrors('title');

        $this->patch($thread->path(), [
            'title' => 'changed',
        ])->assertSessionHasErrors('body');
    }

    /** @test */
    public function unauthorized_users_may_not_update_threads()
    {
        $thread = create('App\Thread', ['user_id' => create('App\User')->id]);
        $this->patch($thread->path(), [])->assertStatus(403);
    }
}
