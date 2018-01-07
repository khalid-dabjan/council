<?php

namespace Tests\Feature;

use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function a_subscribed_user_will_get_a_notification_when_a_reply_is_left_if_the_reply_wasnt_by_the_current_user()
    {
        $thread = create('App\Thread')->subscribe();

        $this->assertCount(0, auth()->user()->notifications);

        //reply by the same user
        $thread->addReply([
            'body' => 'some body',
            'user_id' => auth()->id()
        ]);
        $this->assertCount(0, auth()->user()->fresh()->notifications);

        //reply by another user
        $thread->addReply([
            'body' => 'some body',
            'user_id' => create('App\User')->id
        ]);
        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /** @test */
    public function a_user_can_fetch_their_unread_notifications()
    {
        create(DatabaseNotification::class);

        $this->assertCount(1, $this->getJson("/profiles/" . auth()->user()->name . "/notifications")->json());
    }

    /** @test */
    public function a_user_can_mark_a_notification_as_read()
    {
        create(DatabaseNotification::class);

        tap(auth()->user(), function ($user) {
            $this->assertCount(1, $user->unreadNotifications);
            $this->delete("/profiles/" . $user->name . "/notifications/" . $user->unreadNotifications->first()->id);
            $this->assertCount(0, $user->fresh()->unreadNotifications);
        });
    }
}
