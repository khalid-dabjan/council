<?php

namespace Tests\Feature;

use App\Mail\PleaseConfirmYourEmail;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function when_a_user_registers_an_email_is_sent()
    {
        Mail::fake();

        $this->post('/register', [
            'name' => 'John',
            'email' => 'john@gmail.com',
            'password' => 'foobar',
            'password_confirmation' => 'foobar'
        ]);

        Mail::assertQueued(PleaseConfirmYourEmail::class);
    }

    /** @test */
    public function users_can_fully_confirm_their_email_address()
    {
        Mail::fake();
        $this->post('/register', [
            'name' => 'John',
            'email' => 'john@gmail.com',
            'password' => 'foobar',
            'password_confirmation' => 'foobar'
        ]);

        $user = User::whereName('John')->first();
        $this->assertFalse($user->confirmed);
        $this->assertNotNull($user->confirmation_token);

        $this->get('/register/confirm?token=' . $user->confirmation_token)
            ->assertRedirect('/threads')
            ->assertSessionHas('flash');
        tap($user->fresh(), function ($user) {
            $this->assertTrue($user->confirmed);
            $this->assertNull($user->confirmation_token);
        });


    }

    /** @test */
    public function confirming_an_invalid_token()
    {
        $this->get('/register/confirm?token=invalid')
            ->assertRedirect('/threads')
            ->assertSessionHas('flash');
    }
}
