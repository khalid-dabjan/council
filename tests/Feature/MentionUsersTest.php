<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function mentioned_users_get_notified()
    {
        $john = create('App\User', ['name' => 'JohnDoe']);
        $jane = create('App\User', ['name' => 'JaneDoe']);

        $this->signIn($john);

        $thread = create('App\Thread');
        $reply = make('App\Reply', ['body' => '@JaneDoe check this out']);
        $this->json('post', $thread->path() . '/replies', $reply->toArray());

        $this->assertCount(1, $jane->notifications);
    }

    /** @test */
    public function it_returns_a_list_of_users_that_match_a_search_term()
    {
        create('App\User', ['name' => 'JohnDoe']);
        create('App\User', ['name' => 'JohnDoe2']);
        create('App\User', ['name' => 'SusanDoe']);

        $results = $this->json('get','/api/users',['name'=>'john']);

        $this->assertCount(2,$results->json());
    }
}
