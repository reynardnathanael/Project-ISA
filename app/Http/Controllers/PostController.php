<?php

namespace App\Http\Controllers;

use App\{Category, Post, Tag};
use App\Http\Requests\PostRequest;
use PDF;

class PostController extends Controller
{
    public function index()
    {
        return view('posts.index', [
            'posts' => Post::latest()->paginate(5),
        ]);
    }

    public function show(Post $post)
    {
        $posts = Post::where('category_id', $post->category_id)->latest()->limit(6)->get();
        include('functions.php');
        $src = 'storage/' . $post->thumbnail; // . $post->thumbnail;
        $im = imagecreatefrompng($src);
        $real_message = '';
        for ($x = 0; $x < 64; $x++) {
            $y = $x;
            $rgb = imagecolorat($im, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            $blue = toBin($b);
            $real_message .= $blue[strlen($blue) - 1];
        }
        $real_message = toString($real_message);
        $post->body=decrypt($post->body);
        return view('posts.show', compact('post', 'posts', 'real_message'));
    }

    public function create()
    {
        return view('posts.create', [
            'post' => new Post(),
            'categories' => Category::get(),
            'tags' => Tag::get(),
        ]);
    }

    public function store(PostRequest $request)
    {
        $request->validate([
            'thumbnail' => 'required|image|mimes:png',
        ]);

        $attr = $request->all();

        $slug = \Str::slug(request('title'));
        $attr['slug'] = $slug;


        $thumbnail = request()->file('thumbnail') ? request()->file('thumbnail')->store("images/posts") : null;

        $attr['category_id'] = request('category');
        $attr['thumbnail'] = $thumbnail;
        $attr['body'] = encrypt($request->body);

        // Create new post
        $post = auth()->user()->posts()->create($attr);
        $post->tags()->attach(request('tags'));
        include('functions.php');
        $message_to_hide = request('hidden-message');
        $binary_message = toBin($message_to_hide);
        $message_length = strlen($binary_message);
        $src = 'storage/' . $thumbnail;
        $im = imagecreatefrompng($src);
        for ($x = 0; $x < $message_length; $x++) {
            $y = $x;
            $rgb = imagecolorat($im, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            $newR = $r;
            $newG = $g;
            $newB = toBin($b);
            $newB[strlen($newB) - 1] = $binary_message[$x];
            $newB = toString($newB);

            $new_color = imagecolorallocate($im, $newR, $newG, $newB);
            imagesetpixel($im, $x, $y, $new_color);
        }
        imagepng($im, 'storage/' . $thumbnail);
        imagedestroy($im);

        session()->flash('success', 'The post was created!');
        return redirect('posts');
    }

    public function edit(Post $post)
    {
        $post->body=decrypt($post->body);
        return view('posts.edit', [
            'post' => $post,
            'categories' => Category::get(),
            'tags' => Tag::get(),
        ]);
    }

    public function update(PostRequest $request, Post $post)
    {
        $request->validate([
            'thumbnail' => 'image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);

        $this->authorize('update', $post);
        if (request()->file('thumbnail')) {
            \Storage::delete($post->thumbnail);
            $thumbnail = request()->file('thumbnail')->store("images/posts");
        } else {
            $thumbnail = $post->thumbnail;
        }

        $attr = $request->all();
        $attr['category_id'] = request('category');
        $attr['thumbnail'] = $thumbnail;
        $attr['body'] = encrypt($request->body);

        // Update the post
        $post->update($attr);
        $post->tags()->sync(request('tags'));
        include('functions.php');
        $message_to_hide = request('hidden-message');
        $binary_message = toBin($message_to_hide);
        $message_length = strlen($binary_message);
        $src = 'storage/' . $thumbnail;
        $im = imagecreatefrompng($src);
        for ($x = 0; $x < $message_length; $x++) {
            $y = $x;
            $rgb = imagecolorat($im, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            $newR = $r;
            $newG = $g;
            $newB = toBin($b);
            $newB[strlen($newB) - 1] = $binary_message[$x];
            $newB = toString($newB);

            $new_color = imagecolorallocate($im, $newR, $newG, $newB);
            imagesetpixel($im, $x, $y, $new_color);
        }
        imagepng($im, 'storage/' . $thumbnail);
        imagedestroy($im);

        session()->flash('success', 'The post was updated!');
        return redirect('posts');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        \Storage::delete($post->thumbnail);
        $post->tags()->detach();
        $post->delete();
        session()->flash("error", "The post was deleted");
        return redirect('posts');
    }

    public function print()
    {
        $posts = Post::all();
        $pdf = PDF::loadview('posts.post_pdf', compact('posts'));
        return $pdf->stream("posts.pdf", array("Attachment" => 0));
    }
}
