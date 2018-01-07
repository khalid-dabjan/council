<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SubscribeToThreadTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_subscribe_to_threads()
    {
        $this->signIn();
        $thread = create('App\Thread');
        $this->post($thread->path() . '/subscriptions');

        $this->assertCount(1, $thread->subscriptions);

//        $this->assertCount(0, auth()->user()->notifications);
//        $thread->addReply([
//            'body' => 'some body',
//            'user_id' => auth()->id()
//        ]);
//
//        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /** @test */
    public function a_user_can_unsupscribe_from_a_thread()
    {
        $this->signIn();
        $thread = create('App\Thread');

        $thread->subscribe();
        $this->delete($thread->path() . '/subscriptions');

        $this->assertCount(0, $thread->subscriptions);
    }
}
