<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Models\Like;
use App\Models\Post;
use App\Models\Profile;
use App\Queries\PostThreadQuery;
use App\Queries\TimelineQuery;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index()
    {
        $profile = Auth::user()->profile;

        $posts = TimelineQuery::forViewer($profile)->get();

        return Inertia::render('Posts/Index', [
            'profile' => $profile->toResource(),
            'posts' => $posts->toResourceCollection(),
        ]);
    }

    public function show(Profile $profile, Post $post): Response
    {
        $post = PostThreadQuery::for($post, Auth::user()?->profile)->load();

        return Inertia::render('Posts/Show', [
            'post' => $post->toResource(),
        ]);
    }

    public function store(CreatePostRequest $createPostRequest): \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
    {
        $profile = Auth::user()->profile;

        Post::publish($profile, $createPostRequest->content);

        return to_route('posts.index');
    }

    public function reply(Profile $profile, Post $post, CreatePostRequest $createPostRequest): \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
    {
        $currentProfile = Auth::user()->profile;

        Post::reply($currentProfile, $post, $createPostRequest->content);

        return back();
    }

    public function repost(Profile $profile, Post $post): \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
    {
        $currentProfile = Auth::user()->profile;

        Post::repost($currentProfile, $post);

        return to_route('posts.index');
    }

    public function quote(Profile $profile, Post $post, CreatePostRequest $createPostRequest): \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
    {
        $currentProfile = Auth::user()->profile;

        Post::repost($currentProfile, $post, $createPostRequest->content);

        return to_route('posts.index');
    }

    public function like(Profile $profile, Post $post): \Illuminate\Http\RedirectResponse
    {
        $currentProfile = Auth::user()->profile;

        Like::createLike($currentProfile, $post);

        return back();
    }

    public function unlike(Profile $profile, Post $post): \Illuminate\Http\RedirectResponse
    {
        $currentProfile = Auth::user()->profile;

        Like::removeLike($currentProfile, $post);

        return back();
    }

    public function destroy(Profile $profile, Post $post): \Illuminate\Http\RedirectResponse
    {
        if (Auth::user()->can('update', $post)) {
            $post->delete();
        }

        $post
            ->reposts()
            ->where('profile_id', Auth::user()->profile->id)
            ->first()
            ?->delete();

        return back();
    }
}
