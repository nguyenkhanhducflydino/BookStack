@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Create New Page</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('books.public.show', $book->slug) }}">{{ $book->name }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a
                                        href="{{ route('chapters.public.show', [$book->slug, $chapter->slug]) }}">{{ $chapter->name }}</a>
                                </li>
                                <li class="breadcrumb-item active">Create Page</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pages.store') }}" method="POST">
                            @csrf

                            <!-- Hidden fields for book and chapter -->
                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                            <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">

                            <!-- Page Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Page Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Page Content -->
                            <div class="mb-3">
                                <label for="content" class="form-label">Content</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <ul class="nav nav-tabs" id="editorTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="edit-tab" data-bs-toggle="tab"
                                                    data-bs-target="#edit" type="button" role="tab">
                                                    <i class="bi bi-pencil me-1"></i>Edit
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="preview-tab" data-bs-toggle="tab"
                                                    data-bs-target="#preview" type="button" role="tab">
                                                    <i class="bi bi-eye me-1"></i>Preview
                                                </button>
                                            </li>
                                        </ul>
                                        <div class="tab-content border border-top-0" id="editorTabsContent">
                                            <div class="tab-pane fade show active p-3" id="edit" role="tabpanel">
                                                <textarea class="form-control @error('content') is-invalid @enderror" name="content" rows="15" id="contentEditor"
                                                    placeholder="Write your page content in Markdown...">{{ old('content') }}</textarea>
                                            </div>
                                            <div class="tab-pane fade p-3" id="preview" role="tabpanel">
                                                <div id="previewContent" class="border rounded p-3"
                                                    style="min-height: 300px; background-color: #f8f9fa;">
                                                    <p class="text-muted">Preview will appear here...</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @error('content')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">You can use Markdown syntax for formatting.</small>
                            </div>

                            <!-- Page Options -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="draft" value="1"
                                            {{ old('draft') ? 'checked' : '' }}>
                                        <label class="form-check-label">Save as Draft</label>
                                        <small class="text-muted d-block">Draft pages are not visible to the public</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="priority" class="form-label">Priority (for ordering)</label>
                                    <input type="number" class="form-control @error('priority') is-invalid @enderror"
                                        name="priority" value="{{ old('priority', 0) }}" min="0">
                                    <small class="text-muted">Higher numbers appear first</small>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('chapters.public.show', [$book->slug, $chapter->slug]) }}"
                                    class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Chapter
                                </a>
                                <div>
                                    <button type="submit" name="action" value="save" class="btn btn-success me-2">
                                        <i class="bi bi-check-lg me-2"></i>Create Page
                                    </button>
                                    <button type="submit" name="action" value="save_and_continue"
                                        class="btn btn-primary">
                                        <i class="bi bi-check-lg me-2"></i>Save & Continue Editing
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Chapter: {{ $chapter->name }}</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">{{ $chapter->description }}</p>
                        <p><strong>Book:</strong> {{ $book->name }}</p>
                        <p><strong>Existing Pages:</strong> {{ $chapter->pages->count() }}</p>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Markdown Help</h6>
                    </div>
                    <div class="card-body">
                        <small>
                            <strong>Headers:</strong><br>
                            <code># H1</code><br>
                            <code>## H2</code><br>
                            <code>### H3</code><br><br>

                            <strong>Formatting:</strong><br>
                            <code>**bold**</code><br>
                            <code>*italic*</code><br>
                            <code>`code`</code><br><br>

                            <strong>Lists:</strong><br>
                            <code>- Item 1</code><br>
                            <code>- Item 2</code><br><br>

                            <strong>Links:</strong><br>
                            <code>[Text](URL)</code><br><br>

                            <strong>Images:</strong><br>
                            <code>![Alt](URL)</code>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contentEditor = document.getElementById('contentEditor');
            const previewContent = document.getElementById('previewContent');
            const previewTab = document.getElementById('preview-tab');

            // Preview functionality
            previewTab.addEventListener('click', function() {
                const content = contentEditor.value;
                if (content.trim()) {
                    // Simple markdown preview (you can enhance this with a proper markdown parser)
                    let html = content
                        .replace(/^### (.*$)/gm, '<h3>$1</h3>')
                        .replace(/^## (.*$)/gm, '<h2>$1</h2>')
                        .replace(/^# (.*$)/gm, '<h1>$1</h1>')
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\*(.*?)\*/g, '<em>$1</em>')
                        .replace(/`(.*?)`/g, '<code>$1</code>')
                        .replace(/^\- (.*$)/gm, '<li>$1</li>')
                        .replace(/\n/g, '<br>');

                    // Wrap list items
                    html = html.replace(/(<li>.*<\/li>)/g, '<ul>$1</ul>');

                    previewContent.innerHTML = html ||
                        '<p class="text-muted">Preview will appear here...</p>';
                } else {
                    previewContent.innerHTML = '<p class="text-muted">Preview will appear here...</p>';
                }
            });
        });
    </script>
@endsection
