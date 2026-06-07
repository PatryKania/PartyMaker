<?php

use App\Models\User;

test('root login route redirects to dashboard login page', function () {
    $this->get('/login')
        ->assertRedirect('/dashboard/login');
});

test('dashboard login page is accessible for guests', function () {
    $this->get('/dashboard/login')
        ->assertOk()
        ->assertSee('Google')
        ->assertSee('Facebook');
});

test('dashboard redirects authenticated user away from login page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard/login')
        ->assertRedirect('/dashboard');
});

test('locale switch stores supported locale in session', function () {
    $this->from('/dashboard/login')
        ->get(route('locale.switch', 'en'))
        ->assertRedirect('/dashboard/login')
        ->assertSessionHas('locale', 'en');
});

test('locale switch ignores unsupported locale', function () {
    $this->from('/dashboard/login')
        ->get(route('locale.switch', 'de'))
        ->assertRedirect('/dashboard/login')
        ->assertSessionMissing('locale');
});

test('social login callback failures currently redirect to admin login instead of dashboard login', function (string $url) {
    $this->get($url)
        ->assertRedirect('/admin/login')
        ->assertSessionHas('error');
})->with([
    'google callback' => '/auth/google/callback',
    'facebook callback' => '/auth/facebook/callback',
]);

