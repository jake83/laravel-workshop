<?php

use App\Models\Post;
use App\Models\Profile;

test('shows a single post', function () {
    $profile = Profile::factory()->create();
    $post = Post::factory()->for($profile)->create();

    $otherProfile = Profile::factory()->create();
    Post::factory(2)->for($otherProfile)->create(); // 2 posts

    $this->actingAs($profile->user);

    $profile->follow($otherProfile);

    visit(route('posts.index'))
        ->click('@visit-post-link')
        ->assertSee($post->content);
});
