<?php

namespace Tests\Feature;

use App\Activity;
use App\Rules\Recaptcha;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateThreadTest extends TestCase
{

    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();
        app()->singleton(Recaptcha::class, function () {
            $m = \Mockery::mock(Recaptcha::class);
            $m->shouldReceive('passes')->andReturn(true);
            return $m;
        });
    }

    /** @test */
    public function an_user_user_can_create_new_thread()
    {
        $response = $this->publishAThread(['title' => 'some title', 'body' => 'some body']);
        $this->get($response->headers->get('location'))
            ->assertSee('some title')
            ->assertSee('some body');
    }

    /** @test */
    public function authenticated_users_must_confirm_their_email_address_before_creating_threads()
    {
        $user = factory('App\User')->states('unconfirmed')->create();

        $this->signIn($user);

        $thread = make('App\Thread');

        $this->post('/threads', $thread->toArray())
            ->assertRedirect('/threads')->assertSessionHas('flash');
    }

    /** @test */
    public function a_guest_may_not_create_new_thread()
    {
        $this->withExceptionHandling();

        $this->get('/threads/create')
            ->assertRedirect('/login');
        $this->post('/threads')
            ->assertRedirect('/login');
    }

    /** @test */
    public function a_thread_requires_a_title()
    {
        $this->publishAThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_thread_required_a_body()
    {
        $this->publishAThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function a_thread_requires_a_valid_recaptcha()
    {
        unset(app()[Recaptcha::class]);
        $this->publishAThread(['g-recaptcha-response' => 'test'])
            ->assertSessionHasErrors('g-recaptcha-response');
    }

    /** @test */
    public function a_thread_required_a_valid_channel()
    {
        factory('App\Channel', 2)->create();

        $this->publishAThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishAThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    /** @test */
    public function a_thread_should_have_a_unique_slug()
    {
        $this->signIn();
        $thread = create('App\Thread', ['title' => 'Foo title']);

        $this->post('/threads', $thread->toArray() + ['g-recaptcha-response' => 'token']);

        $this->assertEquals($thread->fresh()->slug, 'foo-title');

        $response = $this->postJson('/threads', $thread->toArray() + ['g-recaptcha-response' => 'token'])->json();
        $this->assertEquals("foo-title-{$response['id']}", $response['slug']);
    }

    /** @test */
    public function a_thread_with_number_at_the_end_of_the_title_should_generate_a_proper_slug()
    {
        $this->signIn();
        $thread = create('App\Thread', ['title' => 'Foo title 24']);

        $this->assertEquals($thread->fresh()->slug, 'foo-title-24');

        $response = $this->postJson('/threads', $thread->toArray() + ['g-recaptcha-response' => 'token'])->json();
//        dd($response);
        $this->assertEquals("foo-title-24-{$response['id']}", $response['slug']);
    }

    /** @test */
    public function unauthorized_users_may_not_delete_threads()
    {
        $this->withExceptionHandling();
        $thread = create('App\Thread');

        $this->delete($thread->path())
            ->assertRedirect('/login');

        $this->signIn();
        $this->delete($thread->path())
            ->assertStatus(403);
    }

    /** @test */
    public function authorized_users_may_delete_threads()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $this->json('DELETE', $thread->path())
            ->assertStatus(204);

        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        $this->assertEquals(0, Activity::count());
    }

    public function publishAThread($overrides = [])
    {
        $this->withExceptionHandling()->signIn();

        $thread = make('App\Thread', $overrides);

        return $this->post('/threads', $thread->toArray() + ['g-recaptcha-response' => 'token']);
    }

}
