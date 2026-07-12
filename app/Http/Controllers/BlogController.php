<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;

class BlogController extends Controller
{
    public function index()
    {
        $query = Blog::published()
            ->with(['category', 'author']);

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if ($categoryId = request('category')) {
            $query->where('blog_category_id', $categoryId);
        }

        switch (request('sort')) {
            case 'oldest':
                $query->oldest('published_at');
                break;
            default:
                $query->latest('published_at');
                break;
        }

        $blogs = $query->paginate(20)->withQueryString();

        $categories = BlogCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('app.blog.index', compact('blogs', 'categories'));
    }

    public function show(Blog $blog)
    {
        abort_if(! $blog->is_active || ! $blog->published_at || $blog->published_at->isFuture(), 404);

        $blog->load(['category', 'author']);

        $related = Blog::published()
            ->where('id', '!=', $blog->id)
            ->where('blog_category_id', $blog->blog_category_id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('app.blog.show', compact('blog', 'related'));
    }
}
