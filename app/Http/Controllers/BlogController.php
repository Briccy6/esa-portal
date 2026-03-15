<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogImage;
use App\Models\BlogDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $blogs = Blog::with(['author', 'images', 'documents'])
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->category, function($query, $category) {
                $query->where('category', $category);
            })
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%")
                      ->orWhere('tags', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = Blog::distinct()->pluck('category')->filter();
        $stats = [
            'total' => Blog::count(),
            'published' => Blog::where('status', 'published')->count(),
            'draft' => Blog::where('status', 'draft')->count(),
            'archived' => Blog::where('status', 'archived')->count(),
        ];

        return view('admin.blogs.index', compact('blogs', 'categories', 'stats'));
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

    public function store(Request $request)
    {
        // Enhanced authentication check with session debugging
        \Log::info('=== BLOG STORE METHOD DEBUG ===');
        \Log::info('Request URL: ' . $request->fullUrl());
        \Log::info('Request Method: ' . $request->method());
        \Log::info('Session ID: ' . session()->getId());
        \Log::info('Session exists: ' . (session()->isStarted() ? 'YES' : 'NO'));
        \Log::info('Session data keys: ' . json_encode(array_keys(session()->all())));
        
        // Check admin authentication
        $adminAuthenticated = auth('admin')->check();
        \Log::info('Admin authenticated: ' . ($adminAuthenticated ? 'YES' : 'NO'));
        
        if (!$adminAuthenticated) {
            \Log::error('Admin not authenticated in blog store method');
            \Log::error('All session data: ' . json_encode(session()->all()));
            
            // Try to re-authenticate with session data
            $adminId = session()->get('admin_id');
            if ($adminId) {
                \Log::info('Found admin_id in session: ' . $adminId);
                // Try to manually log in the admin
                $admin = \App\Models\Admin::find($adminId);
                if ($admin) {
                    \Log::info('Found admin user, attempting manual login');
                    auth('admin')->login($admin);
                    \Log::info('Manual login successful: ' . (auth('admin')->check() ? 'YES' : 'NO'));
                }
            }
            
            // If still not authenticated, redirect to login
            if (!auth('admin')->check()) {
                return redirect()->route('admin.login.form')
                    ->with('error', 'You must be logged in as an admin to create blogs.')
                    ->with('intended', $request->fullUrl());
            }
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'status' => 'required|in:draft,published',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:5120',
            'image_captions' => 'nullable|array',
            'image_alts' => 'nullable|array',
        ]);

        $adminId = auth('admin')->id();
        \Log::info('Creating blog with admin ID: ' . $adminId);

        $blog = Blog::create([
            'title' => $request->title,
            'slug' => $this->generateUniqueSlug($request->title),
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'status' => $request->status,
            'published_at' => $request->status === 'published' ? now() : null,
            'author_id' => $adminId,
            'category' => $request->category,
            'tags' => $request->tags,
            'meta_title' => $request->meta_title ?: $request->title,
            'meta_description' => $request->meta_description,
        ]);

        // Handle featured image
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('blogs/featured', 'public');
            $blog->featured_image = $path;
            $blog->save();
        }

        // Handle multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $imagePath = $image->store('blogs/images', 'public');
                
                BlogImage::create([
                    'blog_id' => $blog->id,
                    'image_path' => $imagePath,
                    'image_alt' => $request->image_alts[$index] ?? null,
                    'image_caption' => $request->image_captions[$index] ?? null,
                    'sort_order' => $index,
                ]);
            }
        }

        // Handle documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $documentPath = $document->store('blogs/documents', 'public');
                
                BlogDocument::create([
                    'blog_id' => $blog->id,
                    'document_path' => $documentPath,
                    'document_name' => $document->getClientOriginalName(),
                    'document_size' => $document->getSize(),
                    'document_type' => $document->getClientMimeType(),
                ]);
            }
        }

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Blog created successfully!');
    }

    public function edit(Blog $blog)
    {
        $blog->load(['images' => function($query) {
            $query->orderBy('sort_order');
        }, 'documents']);

        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        // Debug: Check authentication state
        if (!auth('admin')->check()) {
            \Log::error('Admin not authenticated in blog update method');
            \Log::error('Session data: ' . json_encode(session()->all()));
            return redirect()->route('admin.login.form')
                ->with('error', 'You must be logged in as an admin to update blogs.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'status' => 'required|in:draft,published,archived',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:5120',
            'image_captions' => 'nullable|array',
            'image_alts' => 'nullable|array',
        ]);

        $blog->update([
            'title' => $request->title,
            'slug' => $request->title !== $blog->title ? $this->generateUniqueSlug($request->title) : $blog->slug,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'status' => $request->status,
            'published_at' => $request->status === 'published' && !$blog->published_at ? now() : $blog->published_at,
            'category' => $request->category,
            'tags' => $request->tags,
            'meta_title' => $request->meta_title ?: $request->title,
            'meta_description' => $request->meta_description,
        ]);

        // Handle featured image
        if ($request->hasFile('featured_image')) {
            if ($blog->featured_image) {
                Storage::disk('public')->delete($blog->featured_image);
            }
            $path = $request->file('featured_image')->store('blogs/featured', 'public');
            $blog->featured_image = $path;
            $blog->save();
        }

        // Handle new images
        if ($request->hasFile('images')) {
            $maxSortOrder = $blog->images()->max('sort_order') ?? 0;
            foreach ($request->file('images') as $index => $image) {
                $imagePath = $image->store('blogs/images', 'public');
                
                BlogImage::create([
                    'blog_id' => $blog->id,
                    'image_path' => $imagePath,
                    'image_alt' => $request->image_alts[$index] ?? null,
                    'image_caption' => $request->image_captions[$index] ?? null,
                    'sort_order' => $maxSortOrder + $index + 1,
                ]);
            }
        }

        // Handle new documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $documentPath = $document->store('blogs/documents', 'public');
                
                BlogDocument::create([
                    'blog_id' => $blog->id,
                    'document_path' => $documentPath,
                    'document_name' => $document->getClientOriginalName(),
                    'document_size' => $document->getSize(),
                    'document_type' => $document->getClientMimeType(),
                ]);
            }
        }

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Blog updated successfully!');
    }

    public function destroy(Blog $blog)
    {
        // Delete associated files
        if ($blog->featured_image) {
            Storage::disk('public')->delete($blog->featured_image);
        }

        foreach ($blog->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        foreach ($blog->documents as $document) {
            Storage::disk('public')->delete($document->document_path);
        }

        $blog->delete();

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Blog deleted successfully!');
    }

    public function toggleFeatured(Blog $blog)
    {
        $blog->is_featured = !$blog->is_featured;
        $blog->save();

        return response()->json([
            'success' => true,
            'featured' => $blog->is_featured
        ]);
    }

    public function deleteImage(BlogImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(['success' => true]);
    }

    public function deleteDocument(BlogDocument $document)
    {
        Storage::disk('public')->delete($document->document_path);
        $document->delete();

        return response()->json(['success' => true]);
    }

    // Public methods
    public function publicIndex(Request $request)
    {
        $blogs = Blog::with(['author', 'images'])
            ->where('status', 'published')
            ->when($request->category, function($query, $category) {
                $query->where('category', $category);
            })
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%")
                      ->orWhere('tags', 'like', "%{$search}%");
                });
            })
            ->orderBy('published_at', 'desc')
            ->paginate(9);

        $featuredBlogs = Blog::with(['author', 'images'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        $categories = Blog::where('status', 'published')
            ->distinct()
            ->pluck('category')
            ->filter();

        return view('blogs.index', compact('blogs', 'featuredBlogs', 'categories'));
    }

    public function show(Blog $blog)
    {
        if ($blog->status !== 'published') {
            abort(404);
        }

        $blog->incrementViews();
        $blog->load(['author', 'images' => function($query) {
            $query->orderBy('sort_order');
        }, 'documents']);

        $relatedBlogs = Blog::with(['author', 'images'])
            ->where('status', 'published')
            ->where('id', '!=', $blog->id)
            ->where(function($query) use ($blog) {
                $query->where('category', $blog->category)
                      ->orWhereIn('id', function($q) use ($blog) {
                          $q->select('blog_id')
                            ->from('blog_images')
                            ->whereIn('blog_id', 
                                Blog::where('category', $blog->category)
                                    ->where('status', 'published')
                                    ->pluck('id')
                            );
                      });
            })
            ->orderBy('published_at', 'desc')
            ->take(4)
            ->get();

        return view('blogs.show', compact('blog', 'relatedBlogs'));
    }

    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (Blog::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
