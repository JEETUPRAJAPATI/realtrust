<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Yoeunes\Toastr\Facades\Toastr;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->withCount('comments')->with('staff')->get();
        return view('staff.posts.index', compact('posts'));
    }


    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('staff.posts.create', compact('categories', 'tags'));
    }


    public function store(Request $request)
    {
        
        $request->validate([
            'title'     => 'required|unique:posts|max:255',
            'image'     => 'required|mimes:jpeg,jpg,png',
            'categories' => 'required',
            'tags'      => 'required',
            'body'      => 'required'
        ]);

        $image = $request->file('image');
        $slug  = Str::slug($request->title);
        
        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('posts')) {
                Storage::disk('public')->makeDirectory('posts');
            }
            $postimage = Image::make($image)->stream();
            Storage::disk('public')->put('posts/' . $imagename, $postimage);
        } else {
            $imagename = '';
        }

        $post = new Post();
        $post->user_id = Auth::guard('staff')->id();
        $post->title = $request->title;
        $post->slug = $slug;
        $post->image = $imagename;
        $post->body = $request->body;
        if (isset($request->status)) {
            $post->status = true;
        }
        $post->is_approved = true;
        $post->save();

        $post->categories()->attach($request->categories);
        $post->tags()->attach($request->tags);
        
        Toastr::success('message', 'Post created successfully.');
        return redirect()->route('staff.posts.index');
    }


    public function show($slug)
    {
        $post = Post::withCount('comments')->where('slug', $slug)->firstOrFail();
        return view('staff.posts.show', compact('post'));
    }


    public function edit($slug)
    {
        $categories = Category::all();
        $tags = Tag::all();
        $post = Post::withCount('comments')->where('slug', $slug)->firstOrFail();
        $selectedtag = $post->tags->pluck('id')->toArray();
        $selectedcategories = $post->categories->pluck('id')->toArray();
        // $selectedCategories=$post->categories->pluck('id');
        // dd($selectedtag, $categories);
        // dd($post);
        return view('staff.posts.edit', compact('categories', 'tags', 'post', 'selectedtag','selectedcategories'));
    }


    public function update(Request $request, $slug)
    {
        $request->validate([
            'title'     => 'required|max:255',
            'image'     => 'mimes:jpeg,jpg,png',
            'categories' => 'required',
            'tags'      => 'required',
            'body'      => 'required'
        ]);

        $image = $request->file('image');
        $slug  = Str::slug($request->title);

        $post = Post::where('slug', $slug)->firstOrFail();

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('posts')) {
                Storage::disk('public')->makeDirectory('posts');
            }
            if (Storage::disk('public')->exists('posts/' . $post->image)) {
                Storage::disk('public')->delete('posts/' . $post->image);
            }
            $postimage =  Image::make($image)->stream();
            Storage::disk('public')->put('posts/' . $imagename, $postimage);
        } else {
            $imagename = $post->image;
        }

        $post->user_id = Auth::guard('staff')->id();
        $post->title = $request->title;
        $post->slug = $slug;
        $post->image = $imagename;
        $post->body = $request->body;
        if (isset($request->status)) {
            $post->status = true;
        } else {
            $post->status = false;
        }
        $post->is_approved = true;
        $post->save();

        $post->categories()->sync($request->categories);
        $post->tags()->sync($request->tags);

        Toastr::success('message', 'Post updated successfully.');
        return redirect()->route('staff.posts.index');
    }


    public function destroy($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        if (Storage::disk('public')->exists('posts/' . $post->image)) {
            Storage::disk('public')->delete('posts/' . $post->image);
        }
        $post->delete();
        $post->categories()->detach();
        $post->tags()->detach();
        $post->comments()->delete();

        Toastr::success('message', 'Post deleted successfully.');
        return back();
    }
}
