<?php

use App\Models\Profile;

test('it follows another profile', function () {
    $profile = Profile::factory()->create();
    $otherProfile = Profile::factory()->create();

    $this->actingAs($profile->user);

    visit(route('profiles.show', $otherProfile))
        ->click('@follow-button')
        ->assertSee('you are now following '.$otherProfile->handle);

    expect($profile->followings)->toHaveCount(1);
});
