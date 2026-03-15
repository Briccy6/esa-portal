<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogImage;
use App\Models\BlogDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogs = Blog::with(['images', 'documents'])->latest()->paginate(10);
        return view('admin.blogs.index', compact('blogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.blogs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx|max:5120',
        ]);

        $blog = Blog::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'status' => $request->status,
            'is_featured' => $request->boolean('featured'),
        ]);

        // Handle images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('blogs/images', 'public');
                BlogImage::create([
                    'blog_id' => $blog->id,
                    'image_path' => $path,
                    'caption' => $request->image_captions[$index] ?? null,
                    'alt_text' => $request->image_alt_texts[$index] ?? null,
                    'sort_order' => $index,
                ]);
            }
        }

        // Handle documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('blogs/documents', 'public');
                BlogDocument::create([
                    'blog_id' => $blog->id,
                    'document_path' => $path,
                    'document_name' => $document->getClientOriginalName(),
                    'file_size' => $document->getSize(),
                    'mime_type' => $document->getMimeType(),
                ]);
            }
        }

        return redirect()->route('admin.blogs.index')->with('success', 'Blog created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        $blog->load(['images', 'documents', 'relatedPosts']);
        return view('blogs.show', compact('blog'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blog $blog)
    {
        $blog->load(['images', 'documents']);
        return view('admin.blogs.edit', compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx|max:5120',
        ]);

        $blog->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'status' => $request->status,
            'is_featured' => $request->boolean('featured'),
        ]);

        // Handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('blogs/images', 'public');
                BlogImage::create([
                    'blog_id' => $blog->id,
                    'image_path' => $path,
                    'caption' => $request->image_captions[$index] ?? null,
                    'alt_text' => $request->image_alt_texts[$index] ?? null,
                    'sort_order' => $blog->images()->count() + $index,
                ]);
            }
        }

        // Handle new documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('blogs/documents', 'public');
                BlogDocument::create([
                    'blog_id' => $blog->id,
                    'document_path' => $path,
                    'document_name' => $document->getClientOriginalName(),
                    'file_size' => $document->getSize(),
                    'mime_type' => $document->getMimeType(),
                ]);
            }
        }

        return redirect()->route('admin.blogs.index')->with('success', 'Blog updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        // Delete images
        foreach ($blog->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        // Delete documents
        foreach ($blog->documents as $document) {
            Storage::disk('public')->delete($document->document_path);
            $document->delete();
        }

        $blog->delete();

        return redirect()->route('admin.blogs.index')->with('success', 'Blog deleted successfully.');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Blog $blog)
    {
        $blog->update(['featured' => !$blog->featured]);
        
        return response()->json([
            'success' => true,
            'featured' => $blog->featured,
            'message' => $blog->featured ? 'Blog marked as featured' : 'Blog removed from featured'
        ]);
    }

    /**
     * Delete an image
     */
    public function deleteImage(BlogImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        
        return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
    }

    /**
     * Delete a document
     */
    public function deleteDocument(BlogDocument $document)
    {
        Storage::disk('public')->delete($document->document_path);
        $document->delete();
        
        return response()->json(['success' => true, 'message' => 'Document deleted successfully']);
    }

    /**
     * Display public blog index
     */
    public function publicIndex()
    {
        $featuredBlogs = Blog::published()->featured()->latest()->take(3)->get();
        $blogs = Blog::published()->latest()->paginate(9);
        
        // Increment view count for each blog
        foreach ($blogs as $blog) {
            $blog->increment('views');
        }
        
        return view('blogs.index', compact('blogs', 'featuredBlogs'));
    }

    /**
     * Increment blog view count
     */
    public function incrementViewCount(Blog $blog)
    {
        $blog->increment('views');
        return response()->json(['success' => true]);
    }
}
