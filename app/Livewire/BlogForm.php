<?php

namespace App\Livewire;

use App\Models\Blog;
use App\Models\BlogImage;
use App\Models\BlogDocument;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BlogForm extends Component
{
    use WithFileUploads;

    public $blog;
    public $title;
    public $excerpt;
    public $content;
    public $status = 'draft';
    public $category;
    public $tags;
    public $meta_title;
    public $meta_description;
    public $featured_image;
    public $images = [];
    public $documents = [];
    public $image_captions = [];
    public $image_alts = [];

    protected $rules = [
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
    ];

    protected $messages = [
        'title.required' => 'The blog title is required.',
        'content.required' => 'The blog content is required.',
        'status.required' => 'The status is required.',
        'featured_image.image' => 'The featured image must be an image.',
        'featured_image.mimes' => 'The featured image must be a file of type: jpg, jpeg, png, gif.',
        'images.*.image' => 'Each image must be an image file.',
        'images.*.mimes' => 'Each image must be a file of type: jpg, jpeg, png, gif.',
        'documents.*.mimes' => 'Each document must be a file of type: pdf, doc, docx, xls, xlsx, ppt, pptx.',
    ];

    public function mount($blogId = null)
    {
        if ($blogId) {
            $this->blog = Blog::with(['images' => function($query) {
                $query->orderBy('sort_order');
            }, 'documents'])->findOrFail($blogId);
            
            $this->title = $this->blog->title;
            $this->excerpt = $this->blog->excerpt;
            $this->content = $this->blog->content;
            $this->status = $this->blog->status;
            $this->category = $this->blog->category;
            $this->tags = $this->blog->tags;
            $this->meta_title = $this->blog->meta_title;
            $this->meta_description = $this->blog->meta_description;
        } else {
            $this->blog = new Blog();
        }
    }

    public function save()
    {
        $this->validate();

        $blogData = [
            'title' => $this->title,
            'slug' => $this->generateUniqueSlug($this->title),
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'status' => $this->status,
            'published_at' => $this->status === 'published' ? now() : null,
            'author_id' => Auth::guard('admin')->id(),
            'category' => $this->category,
            'tags' => $this->tags,
            'meta_title' => $this->meta_title ?: $this->title,
            'meta_description' => $this->meta_description,
        ];

        if ($this->blog->id) {
            // Update existing blog
            $this->blog->update($blogData);
            $blog = $this->blog;
        } else {
            // Create new blog
            $blog = Blog::create($blogData);
        }

        // Handle featured image
        if ($this->featured_image) {
            if ($blog->featured_image) {
                Storage::disk('public')->delete($blog->featured_image);
            }
            $path = $this->featured_image->store('blogs/featured', 'public');
            $blog->featured_image = $path;
            $blog->save();
        }

        // Handle multiple images
        if (count($this->images) > 0) {
            $maxSortOrder = $blog->images()->max('sort_order') ?? 0;
            foreach ($this->images as $index => $image) {
                $imagePath = $image->store('blogs/images', 'public');
                
                BlogImage::create([
                    'blog_id' => $blog->id,
                    'image_path' => $imagePath,
                    'image_alt' => $this->image_alts[$index] ?? null,
                    'image_caption' => $this->image_captions[$index] ?? null,
                    'sort_order' => $maxSortOrder + $index + 1,
                ]);
            }
        }

        // Handle documents
        if (count($this->documents) > 0) {
            foreach ($this->documents as $document) {
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

        $this->dispatch('blog-saved', [
            'type' => 'success',
            'message' => $this->blog->id ? 'Blog updated successfully!' : 'Blog created successfully!',
            'blogId' => $blog->id
        ]);

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'title', 'excerpt', 'content', 'status', 'category', 'tags',
            'meta_title', 'meta_description', 'featured_image', 'images', 
            'documents', 'image_captions', 'image_alts'
        ]);
        $this->blog = new Blog();
    }

    public function deleteImage($imageId)
    {
        $image = BlogImage::find($imageId);
        if ($image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
            
            $this->dispatch('show-message', [
                'type' => 'success',
                'message' => 'Image deleted successfully!'
            ]);
        }
    }

    public function deleteDocument($documentId)
    {
        $document = BlogDocument::find($documentId);
        if ($document) {
            Storage::disk('public')->delete($document->document_path);
            $document->delete();
            
            $this->dispatch('show-message', [
                'type' => 'success',
                'message' => 'Document deleted successfully!'
            ]);
        }
    }

    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (Blog::where('slug', $slug)->where('id', '!=', $this->blog->id ?? 0)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function render()
    {
        return view('livewire.blog-form');
    }
}
