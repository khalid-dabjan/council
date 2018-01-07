<?php

namespace Tests\Unit;


use App\Notifications\ThreadWasUpdated;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ThreadTest extends TestCase
{

    use DatabaseMigrations;

    protected $thread;

    public function setUp()
    {
        parent::setUp();

        $this->thread = create('App\Thread');
    }

    /** @test */
    public function a_thread_can_make_a_string_path()
    {
        $thread = create('App\Thread');
        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->slug}", $thread->path());
    }

    /** @test */
    public function a_thread_has_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    /** @test */
    public function a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
            'body' => 'foobar',
            'user_id' => 1
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    /** @test */
    public function a_notification_is_send_to_thread_subscribers_when_a_reply_is_added()
    {
        Notification::fake();
        $this->signIn();
        $this->thread->subscribe();
        $this->thread->addReply([
            'body' => 'foobar',
            'user_id' => 999
        ]);
        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);
    }

    /** @test */
    public function a_thread_has_creator()
    {
        $this->assertInstanceOf('App\User', $this->thread->creator);
    }

    /** @test */
    public function a_thread_has_a_channel()
    {
        $thread = create('App\Thread');
        $this->assertInstanceOf('App\Channel', $thread->channel);
    }

    /** @test */
    public function it_knows_if_the_authenricated_user_is_subscribed()
    {
        $thread = create('App\Thread');

        $this->signIn();

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }

    /** @test */
    public function a_thread_knows_that_new_replies_has_been_left_since_a_user_viewed_it()
    {
        $thread = create('App\Thread');

        $this->signIn();
        $user = auth()->user();
        $this->assertTrue($thread->isUpdatedFor($user));
        $user->read($thread);
        $this->assertFalse($thread->isUpdatedFor($user));
    }

    /** @test */
    public function it_records_the_number_of_visits()
    {
        $thread = make('App\Thread', ['id' => 1]);
        $thread->visits()->reset();
        $this->assertSame(0, $thread->visits()->count());

        $thread->visits()->record();
        $this->assertEquals(1, $thread->visits()->count());

        $thread->visits()->record();
        $this->assertEquals(2, $thread->visits()->count());
    }

    /** @test */
    public function a_thread_body_is_sanitized()
    {
        $thread = make('App\Thread', ['body' => "<script>alert('bac')</script><p>This is fine</p>"]);

        $this->assertEquals("<p>This is fine</p>", $thread->body);
    }

}
