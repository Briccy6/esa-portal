<?php

namespace App\Livewire;

use App\Models\Blog;
use Livewire\WithPagination;
use Livewire\Component;

class BlogIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $category = '';
    public $perPage = 10;
    public $isUploading = false;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $selectedBlogId = null;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'category' => ['except' => ''],
        'perPage' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function render()
    {
        $blogs = Blog::with(['author', 'images', 'documents'])
            ->when($this->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($this->category, function($query, $category) {
                $query->where('category', $category);
            })
            ->when($this->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%")
                      ->orWhere('tags', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $categories = Blog::distinct()->pluck('category')->filter();
        $stats = [
            'total' => Blog::count(),
            'published' => Blog::where('status', 'published')->count(),
            'draft' => Blog::where('status', 'draft')->count(),
            'archived' => Blog::where('status', 'archived')->count(),
        ];

        return view('livewire.blog-index', compact('blogs', 'categories', 'stats'));
    }

    public function deleteBlog($blogId)
    {
        $blog = Blog::find($blogId);
        if (!$blog) {
            $this->dispatch('show-message', [
                'type' => 'error',
                'message' => 'Blog not found.'
            ]);
            return;
        }

        // Delete associated files
        if ($blog->featured_image) {
            \Storage::disk('public')->delete($blog->featured_image);
        }

        foreach ($blog->images as $image) {
            \Storage::disk('public')->delete($image->image_path);
        }

        foreach ($blog->documents as $document) {
            \Storage::disk('public')->delete($document->document_path);
        }

        $blog->delete();

        $this->dispatch('show-message', [
            'type' => 'success',
            'message' => 'Blog deleted successfully!'
        ]);
    }

    public function toggleFeatured($blogId)
    {
        $blog = Blog::find($blogId);
        if (!$blog) {
            return;
        }

        $blog->is_featured = !$blog->is_featured;
        $blog->save();

        $this->dispatch('show-message', [
            'type' => 'success',
            'message' => $blog->is_featured ? 'Blog marked as featured!' : 'Blog removed from featured!'
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['search', 'status', 'category']);
        $this->resetPage();
    }
}
