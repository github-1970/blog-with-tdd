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
                'comment' => __('User not allowed to edit this comment!')
            ], 'comment_error');
        }

        $commentData = $request->validated();
        $commentData['user_id'] = auth()->id();
        if(!$comment){
            return Redirect::back()->withErrors(__('Comment not found in this post!'));
            // return Redirect::back()->withErrors(__('validation.my_custom.comment-not-found-in-post'));
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
                'comment' => __('User not allowed to delete this comment!')
            ], 'comment_error');
        }

        $comment->delete();
        return Redirect::back();
    }
}
