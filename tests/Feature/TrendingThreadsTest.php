<?php

namespace Tests\Feature;

use App\Trending;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class TrendingThreadsTest extends TestCase
{
    use DatabaseMigrations;

    private $trending;

    protected function setUp()
    {
        parent::setUp();
        $this->trending = new Trending();
        $this->trending->reset();
    }

    /** @test */
    public function it_increments_a_threads_score_each_time_it_is_read()
    {
        $this->assertEmpty($this->trending->get());
        $thread = create('App\Thread');
        $this->call('GET', $thread->path());
        $this->assertCount(1, $this->trending->get());
    }
}
