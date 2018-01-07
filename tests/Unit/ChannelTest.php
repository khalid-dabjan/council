<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChannelTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    public function a_channel_has_a_collection_of_threads()
    {
        $channel = create('App\Channel');

        $thread = create('App\Thread', ['channel_id' => $channel->id]);

        $this->assertTrue($channel->threads->contains($thread));
    }
    
    /** @test */
    public function a_channel_can_make_a_string_path()
    {
        $channel = create('App\Channel');
        $this->assertEquals("/threads/{$channel->slug}", $channel->path());
    }

}
