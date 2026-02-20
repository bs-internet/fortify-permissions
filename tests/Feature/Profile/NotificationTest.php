<?php

use App\Enums\UserStatus;
use App\Models\Notification;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get(route('profile.notifications.index'))
        ->assertRedirect(route('login'));
});

test('notifications page is displayed', function () {
    $user = User::factory()->create(['status' => UserStatus::ACTIVE]);

    $response = $this
        ->actingAs($user)
        ->get(route('profile.notifications.index'));

    $response->assertOk();
});

test('archived notifications page is displayed', function () {
    $user = User::factory()->create(['status' => UserStatus::ACTIVE]);

    $response = $this
        ->actingAs($user)
        ->get(route('profile.notifications.archived'));

    $response->assertOk();
});

test('a notification can be marked as read', function () {
    $user = User::factory()->create(['status' => UserStatus::ACTIVE]);

    $notification = Notification::create([
        'type' => 'App\\Notifications\\TestNotification',
        'notifiable_id' => $user->id,
        'notifiable_type' => User::class,
        'data' => ['title' => 'Test', 'message' => 'Test message'],
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('profile.notifications.markAsRead'), [
            'notification_id' => $notification->id,
        ]);

    $response->assertRedirect();

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('all notifications can be marked as read', function () {
    $user = User::factory()->create(['status' => UserStatus::ACTIVE]);

    Notification::create([
        'type' => 'App\\Notifications\\TestNotification',
        'notifiable_id' => $user->id,
        'notifiable_type' => User::class,
        'data' => ['title' => 'Test 1', 'message' => 'Message 1'],
    ]);

    Notification::create([
        'type' => 'App\\Notifications\\TestNotification',
        'notifiable_id' => $user->id,
        'notifiable_type' => User::class,
        'data' => ['title' => 'Test 2', 'message' => 'Message 2'],
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('profile.notifications.markAllAsRead'));

    $response->assertRedirect();

    expect($user->unreadNotifications()->count())->toBe(0);
});

test('mark as read requires a valid notification id', function () {
    $user = User::factory()->create(['status' => UserStatus::ACTIVE]);

    $response = $this
        ->actingAs($user)
        ->post(route('profile.notifications.markAsRead'), [
            'notification_id' => 'invalid-uuid',
        ]);

    $response->assertSessionHasErrors('notification_id');
});
