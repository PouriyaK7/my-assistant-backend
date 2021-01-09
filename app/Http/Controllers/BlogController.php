<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    protected $banMessage = ['status' => 'ERROR', 'error' => 'You are ban or logged out'];

    protected function isBan() {
        if (\Auth::check())
            return \Auth::user()->ban;

        return 0;
    }

    public function addPost(Request $request) {
        if (!$this->isBan()) {
            $inputs = $request->except('_token');

            Post::create([
                'title' => $inputs['title'],
                'text' => $inputs['text'],
                'user' => \Auth::id(),
                'slug' => $inputs['slug']
            ]);

            return json_encode(['status' => 'OK', 'result' => 'Post added successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function editPost(Request $request, $id) {
        if (!$this->isBan()) {
            $inputs = $request->except('_token');

            Post::where('id', '=', $id)
                ->where('user', '=', \Auth::id())
                ->update([
                    'title' => $inputs['title'],
                    'text' => $inputs['text'],
                    'user' => \Auth::id(),
                    'slug' => $inputs['slug']
                ]);

            return json_encode(['status' => 'OK', 'result' => 'Post edited successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function deletePost($id) {
        if (!$this->isBan()) {
            Post::where('id', '=', $id)
                ->where('user', '=', \Auth::id())
                ->delete();

            return json_encode(['status' => 'OK', 'result' => 'Post deleted successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function addLikeOrDislike($type, $post) {
        if (!$this->isBan()) {
            $comment = PostComment::where('user', '=', \Auth::id())
                ->where('post', '=', $post)
                ->get();

            if (count($comment) != 1)
                PostComment::where('user', '=', \Auth::id())
                    ->where('post', '=', $post)
                    ->delete();
            else {
                if ($comment[0]['type'] == $type)
                    return json_encode(['status' => 'ERROR', 'error' => 'User already sent this comment!']);

                $postModel = new Post($post);
                if ($comment[0]['type'] == 0)
                    $postModel->dislikes--;
                else
                    $postModel->likes--;
                $postModel->save();
            }

            PostComment::create([
                'user' => \Auth::id(),
                'post' => $post,
                'type' => $type,
            ]);

            return json_encode(['status' => 'OK', 'result' => 'Comment added successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function deleteComment($post) {
        if (!$this->isBan()) {
            $comment = PostComment::where('user', '=', \Auth::id())
                ->where('post', '=', $post)
                ->get();
            if (count($comment) != 1
                || Post::where('id', '=', $post)->where('user', '=', \Auth::id())->count() != 1)
                return json_encode(['status' => 'ERROR', 'error' => 'Comment or post does not exist!']);

            $post = new Post($post);
            $comment = $comment[0];

            if ($comment['type'])
                $post->likes--;
            else
                $post->dislikes--;
            $post->save();

            PostComment::where('post', '=', $post->id)
                ->where('user', '=', \Auth::id())
                ->delete();

            return json_encode(['status' => 'OK', 'result' => 'Comment deleted successfully']);
        }

        return json_encode($this->banMessage);
    }
}
