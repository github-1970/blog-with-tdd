<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Http\Requests\Posts\StorePostRequest;
use App\Http\Requests\Posts\UpdatePostRequest;
use App\Models\Tag;
use Illuminate\Support\Facades\Redirect;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $posts = Post::with(['tags', 'comments', 'category'])->published()->get();
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Posts\StorePostRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StorePostRequest $request)
    {
        $postData = $request->validated();
        $postData['user_id'] = auth()->id();
        $post = Post::create($postData);

        if(is_array($postData['tags']) && count($postData['tags']) > 0) {
            foreach ($postData['tags'] as $tagTitle) {
                $post->tags()->attach(
                    Tag::create(['title' => $tagTitle])
                );
            }
        }

        return Redirect::back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Post $post)
    {
        $post->with(['tags', 'comments']);
        $comments = $post->comments;
        $tags = $post->tags;
        $category = $post->category;

        return view('posts.show', compact('post', 'comments', 'tags', 'category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Posts\UpdatePostRequest  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        // check user allowed
        if($post->user_id !== auth()->id()) {
            return Redirect::back()->withErrors([
                'message' => __('this user not allowed perform this operation')
            ], 'post_error');
        }

        // Check data availability
        $postData = $request->validated();
        if(!$postData){
            return Redirect::back()->withErrors(__('content not found'));
        }

        // check tags availability
        if(is_array($postData['tags']) && count($postData['tags']) > 0) {
            foreach ($postData['tags'] as $tagTitle) {
                $post->tags()->sync(
                    Tag::create(['title' => $tagTitle])
                );
            }
        }

        $post->update($postData);
        return Redirect::back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Post $post)
    {
        // comment this following line, because use soft delete, so if you want to restore, all tags not restore
        // $post->tags()->detach();

        // check user allowed
        if($post->user_id !== auth()->id()) {
            return Redirect::back()->withErrors([
                'message' => __('this user not allowed perform this operation')
            ], 'post_error');
        }

        
        $post->delete();
        return Redirect::back();
    }
}
