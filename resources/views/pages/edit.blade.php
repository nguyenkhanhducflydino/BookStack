@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Page: {{ $page->name }}</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('books.public.show', $page->book->slug) }}">{{ $page->book->name }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a
                                        href="{{ route('chapters.public.show', [$page->book->slug, $page->chapter->slug]) }}">{{ $page->chapter->name }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a
                                        href="{{ route('chapter.pages.public.show', [$page->book->slug, $page->chapter->slug, $page->slug]) }}">{{ $page->name }}</a>
                                </li>
                                <li class="breadcrumb-item active">Edit</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pages.update', $page->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Page Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Page Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name', $page->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Chapter Selection -->
                            <div class="mb-3">
                                <label for="chapter_id" class="form-label">Chapter</label>
                                <select class="form-select @error('chapter_id') is-invalid @enderror" name="chapter_id"
                                    required>
                                    <option value="">Select a chapter</option>
                                    @foreach ($page->book->chapters as $chapter)
                                        <option value="{{ $chapter->id }}"
                                            {{ old('chapter_id', $page->chapter_id) == $chapter->id ? 'selected' : '' }}>
                                            {{ $chapter->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('chapter_id')
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
                                                    placeholder="Write your page content in Markdown...">{{ old('content', $page->content) }}</textarea>
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
                                            {{ old('draft', $page->draft) ? 'checked' : '' }}>
                                        <label class="form-check-label">Save as Draft</label>
                                        <small class="text-muted d-block">Draft pages are not visible to the public</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="priority" class="form-label">Priority (for ordering)</label>
                                    <input type="number" class="form-control @error('priority') is-invalid @enderror"
                                        name="priority" value="{{ old('priority', $page->priority ?? 0) }}" min="0">
                                    <small class="text-muted">Higher numbers appear first</small>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('chapter.pages.public.show', [$page->book->slug, $page->chapter->slug, $page->slug]) }}"
                                    class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Page
                                </a>
                                <div>
                                    <button type="submit" name="action" value="save" class="btn btn-success me-2">
                                        <i class="bi bi-check-lg me-2"></i>Update Page
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
                        <h6 class="mb-0">Page Information</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Book:</strong> {{ $page->book->name }}</p>
                        <p><strong>Chapter:</strong> {{ $page->chapter->name }}</p>
                        <p><strong>Created:</strong> {{ $page->created_at->format('M d, Y') }}</p>
                        <p><strong>Last Updated:</strong> {{ $page->updated_at->format('M d, Y H:i') }}</p>
                        @if ($page->createdBy)
                            <p><strong>Created By:</strong> {{ $page->createdBy->name }}</p>
                        @endif
                        @if ($page->updatedBy)
                            <p><strong>Updated By:</strong> {{ $page->updatedBy->name }}</p>
                        @endif
                        <p><strong>Status:</strong>
                            <span class="badge {{ $page->draft ? 'bg-warning text-dark' : 'bg-success' }}">
                                {{ $page->draft ? 'Draft' : 'Published' }}
                            </span>
                        </p>
                        <p><strong>Slug:</strong> <code>{{ $page->slug }}</code></p>
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
                            <code>![Alt](URL)</code><br><br>

                            <strong>Tables:</strong><br>
                            <code>| Col1 | Col2 |</code><br>
                            <code>|------|------|</code><br>
                            <code>| Data | Data |</code>
                        </small>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Danger Zone</h6>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-outline-danger w-100"
                            onclick="deletePage('{{ $page->id }}', '{{ $page->name }}')">
                            <i class="bi bi-trash me-2"></i>Delete Page
                        </button>
                        <small class="text-muted mt-2 d-block">This will permanently delete this page.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Page Modal -->
    <div class="modal fade" id="deletePageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Page</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="pageName"></strong>?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deletePageForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
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
                    // Simple markdown preview (enhanced version)
                    let html = content
                        .replace(/^### (.*$)/gm, '<h3>$1</h3>')
                        .replace(/^## (.*$)/gm, '<h2>$1</h2>')
                        .replace(/^# (.*$)/gm, '<h1>$1</h1>')
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\*(.*?)\*/g, '<em>$1</em>')
                        .replace(/`(.*?)`/g, '<code>$1</code>')
                        .replace(/^\- (.*$)/gm, '<li>$1</li>')
                        .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2">$1</a>')
                        .replace(/!\[(.*?)\]\((.*?)\)/g, '<img src="$2" alt="$1" class="img-fluid">')
                        .replace(/\n\n/g, '</p><p>')
                        .replace(/\n/g, '<br>');

                    // Wrap paragraphs
                    html = '<p>' + html + '</p>';

                    // Wrap list items
                    html = html.replace(/(<li>.*?<\/li>)/gs, '<ul>$1</ul>');

                    // Clean up empty paragraphs
                    html = html.replace(/<p><\/p>/g, '');
                    html = html.replace(/<p><br><\/p>/g, '');

                    previewContent.innerHTML = html ||
                        '<p class="text-muted">Preview will appear here...</p>';
                } else {
                    previewContent.innerHTML = '<p class="text-muted">Preview will appear here...</p>';
                }
            });

            // Load initial preview if content exists
            if (contentEditor.value.trim()) {
                previewTab.click();
                document.getElementById('edit-tab').click(); // Switch back to edit tab
            }
        });

        function deletePage(id, name) {
            document.getElementById('pageName').textContent = name;
            document.getElementById('deletePageForm').action = '/pages/' + id;
            new bootstrap.Modal(document.getElementById('deletePageModal')).show();
        }
    </script>
@endsection
