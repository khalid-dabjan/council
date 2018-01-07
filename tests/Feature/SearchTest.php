<?php

namespace Tests\Feature;

use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_search_for_threads()
    {
        config(['scout.driver' => 'algolia']);
        $keyword = 'foobar';
        create('App\Thread', [], 2);
        create('App\Thread', ['body' => "body with $keyword present"], 2);

        do {
            sleep(.25);
            $results = $this->getJson('threads/search?q=' . $keyword)->json()['data'];
        } while (empty($results));

        $this->assertCount(2, $results);

        Thread::latest()->take(4)->unsearchable();
    }
}
