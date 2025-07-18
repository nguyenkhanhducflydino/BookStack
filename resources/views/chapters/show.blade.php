@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <!-- Chapter Header -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('books.public.show', $book->slug) }}">{{ $book->name }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $chapter->name }}</li>
                    </ol>
                </nav>

                <div class="d-flex align-items-start mb-4">
                    <div class="me-4 bg-primary text-white rounded d-flex align-items-center justify-content-center"
                        style="width: 80px; height: 80px;">
                        <i class="bi bi-collection" style="font-size: 2rem;"></i>
                    </div>

                    <div class="flex-grow-1">
                        <h1>{{ $chapter->name }}</h1>
                        @if ($chapter->description)
                            <p class="text-muted">{{ $chapter->description }}</p>
                        @endif
                        <div class="text-muted">
                            <small>
                                Created {{ $chapter->created_at->diffForHumans() }}
                                @if ($chapter->createdBy)
                                    by {{ $chapter->createdBy->name }}
                                @endif
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Chapter Pages -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Pages in this Chapter</h5>
                        @auth
                            <a href="{{ route('chapter.pages.create', [$book->slug, $chapter->slug]) }}"
                                class="btn btn-sm btn-success">
                                <i class="bi bi-file-plus me-1"></i>Add Page
                            </a>
                        @endauth
                    </div>
                    <div class="card-body">
                        @if ($chapter->pages->count() > 0)
                            @foreach ($chapter->pages as $page)
                                <div class="d-flex align-items-center py-3 border-bottom">
                                    <i class="bi bi-file-text text-success me-3"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="{{ route('chapter.pages.public.show', [$book->slug, $chapter->slug, $page->slug]) }}"
                                                class="text-decoration-none">
                                                {{ $page->name }}
                                            </a>
                                            @if ($page->draft)
                                                <span class="badge bg-warning text-dark ms-2">Draft</span>
                                            @endif
                                        </h6>
                                        @if ($page->content)
                                            <p class="mb-0 small text-muted">
                                                {{ Str::limit(strip_tags($page->content), 150) }}</p>
                                        @endif
                                    </div>
                                    <div class="text-muted small">
                                        {{ $page->updated_at->diffForHumans() }}
                                    </div>
                                    @auth
                                        <div class="ms-3">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('pages.edit', $page->id) }}"
                                                    class="btn btn-outline-secondary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button class="btn btn-outline-danger"
                                                    onclick="deletePage('{{ $page->id }}', '{{ $page->name }}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endauth
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-files text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3 text-muted">No pages in this chapter</h5>
                                <p class="text-muted">Start by creating your first page.</p>
                                @auth
                                    <a href="{{ route('chapter.pages.create', [$book->slug, $chapter->slug]) }}"
                                        class="btn btn-success">
                                        <i class="bi bi-file-plus me-2"></i>Create First Page
                                    </a>
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Chapter Actions</h6>
                    </div>
                    <div class="card-body">
                        @auth
                            <a href="{{ route('chapters.edit', $chapter->id) }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="bi bi-pencil me-2"></i>Edit Chapter
                            </a>
                            <a href="{{ route('chapter.pages.create', [$book->slug, $chapter->slug]) }}"
                                class="btn btn-outline-success w-100 mb-2">
                                <i class="bi bi-file-plus me-2"></i>Add Page
                            </a>
                            <hr>
                            <button class="btn btn-outline-danger w-100"
                                onclick="deleteChapter('{{ $chapter->id }}', '{{ $chapter->name }}')">
                                <i class="bi bi-trash me-2"></i>Delete Chapter
                            </button>
                        @endauth
                    </div>
                </div>

                <!-- Chapter Stats -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="h4 text-success">{{ $chapter->pages->count() }}</div>
                                <small class="text-muted">Pages</small>
                            </div>
                            <div class="col-6">
                                <div class="h4 text-info">{{ $chapter->updated_at->diffForHumans() }}</div>
                                <small class="text-muted">Updated</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @auth
        <!-- Delete Chapter Modal -->
        <div class="modal fade" id="deleteChapterModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Chapter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete <strong id="chapterName"></strong>?</p>
                        <p class="text-danger">This action cannot be undone and will delete all pages in this chapter.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form id="deleteChapterForm" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
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
            function deleteChapter(id, name) {
                document.getElementById('chapterName').textContent = name;
                document.getElementById('deleteChapterForm').action = '/chapters/' + id;
                new bootstrap.Modal(document.getElementById('deleteChapterModal')).show();
            }

            function deletePage(id, name) {
                document.getElementById('pageName').textContent = name;
                document.getElementById('deletePageForm').action = '/pages/' + id;
                new bootstrap.Modal(document.getElementById('deletePageModal')).show();
            }
        </script>
    @endauth
@endsection
