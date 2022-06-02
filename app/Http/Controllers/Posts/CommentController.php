<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Http\Requests\Posts\StoreCommentRequest;
use App\Http\Requests\Posts\UpdateCommentRequest;
use App\Models\Post;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;

class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Posts\StoreCommentRequest  $request
     * @param  \App\Models\Post $post
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCommentRequest $request, Post $post)
    {
        $comment = $request->validated();
        $comment['user_id'] = auth()->id();
        $post->comments()->create($comment);

        return Redirect::back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Posts\UpdateCommentRequest  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCommentRequest $request, Post $post, Comment $comment)
    {
        $comment = $post->comments()->where('id', $comment->id)->first();
        if($comment->user_id !== auth()->id()) {
            return Redirect::back()->withErrors([
                'message' => __('this user not allowed perform this operation')
            ], 'comment_error');
        }

        $commentData = $request->validated();
        $commentData['user_id'] = auth()->id();
        if(!$comment){
            return Redirect::back()->withErrors(__('content not found'));
        }

        $comment->update($commentData);
        return Redirect::back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Post $post, Comment $comment)
    {
        $comment = $post->comments()->where('id', $comment->id)->first();
        // if($comment->user_id !== auth()->id()) {
        if(!Gate::allows('post-comment-actions', $comment)) {
            return Redirect::back()->withErrors([
                'message' => __('this user not allowed perform this operation')
            ], 'comment_error');
        }

        $comment->delete();
        return Redirect::back();
    }
}
