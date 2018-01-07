<?php

namespace Tests\Unit;

use App\Detection\Spam;
use Tests\TestCase;

class SpamTest extends TestCase
{
    /** @test */
    public function it_detects_keywords()
    {
        $spam = new Spam();
        $this->assertFalse($spam->detect('innocent reply'));
    }

    /** @test */
    public function it_detects_key_held()
    {
        $spam = new Spam();

        $this->expectException(\Exception::class);
        $spam->detect('Hello aaaaaaa');
    }
}
