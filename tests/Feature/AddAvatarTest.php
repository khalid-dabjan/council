<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AddAvatarTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function only_members_can_upload_avatars()
    {
        $this->withExceptionHandling();
        $this->json('POST', '/api/users/1/avatar')
            ->assertStatus(401);
    }

    /** @test */
    public function an_avatar_must_be_valid()
    {
        $this->withExceptionHandling()->signIn();
        $this->json('POST', '/api/users/1/avatar', [
            'avatar' => 'not-valid-avatar'
        ])->assertStatus(422);
    }

    /** @test */
    public function a_user_can_upload_an_avatar()
    {
        $this->signIn();

        Storage::fake('public');

        $this->json('POST', '/api/users/1/avatar', [
            'avatar' => $file = UploadedFile::fake()->image('avatar.jpg')
        ]);

        Storage::disk('public')->assertExists("avatars/" . $file->hashName());

        $this->assertEquals(asset('storage/avatars/' . $file->hashName()), auth()->user()->avatar_path);
    }
}
