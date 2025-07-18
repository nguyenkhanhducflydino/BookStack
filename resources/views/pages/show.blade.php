@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <!-- Page Header -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('books.public.show', $book->slug) }}">{{ $book->name }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('chapters.public.show', [$book->slug, $chapter->slug]) }}">{{ $chapter->name }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $page->name }}</li>
                    </ol>
                </nav>

                <div class="d-flex align-items-start mb-4">
                    <div class="me-4 bg-success text-white rounded d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="bi bi-file-text" style="font-size: 1.5rem;"></i>
                    </div>

                    <div class="flex-grow-1">
                        <h1>{{ $page->name }}
                            @if ($page->draft)
                                <span class="badge bg-warning text-dark ms-2">Draft</span>
                            @endif
                        </h1>
                        <div class="text-muted">
                            <small>
                                Last updated {{ $page->updated_at->diffForHumans() }}
                                @if ($page->updatedBy)
                                    by {{ $page->updatedBy->name }}
                                @endif
                            </small>
                        </div>
                    </div>

                    @auth
                        <div class="btn-group" role="group">
                            <a href="{{ route('pages.edit', $page->id) }}" class="btn btn-outline-primary">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                            <button class="btn btn-outline-danger"
                                onclick="deletePage('{{ $page->id }}', '{{ $page->name }}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    @endauth
                </div>

                <!-- Page Content -->
                <div class="card">
                    <div class="card-body">
                        @if ($page->content)
                            <div class="page-content">
                                {!! $page->content_html !!}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-file-text text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3 text-muted">This page is empty</h5>
                                <p class="text-muted">Start adding content to this page.</p>
                                @auth
                                    <a href="{{ route('pages.edit', $page->id) }}" class="btn btn-primary">
                                        <i class="bi bi-pencil me-2"></i>Edit Page
                                    </a>
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Page Navigation -->
                @if ($previousPage || $nextPage)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Navigate Pages</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    @if ($previousPage)
                                        <a href="{{ route('chapter.pages.public.show', [$book->slug, $chapter->slug, $previousPage->slug]) }}"
                                            class="btn btn-outline-secondary w-100">
                                            <i class="bi bi-arrow-left me-2"></i>{{ $previousPage->name }}
                                        </a>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    @if ($nextPage)
                                        <a href="{{ route('chapter.pages.public.show', [$book->slug, $chapter->slug, $nextPage->slug]) }}"
                                            class="btn btn-outline-secondary w-100">
                                            {{ $nextPage->name }}<i class="bi bi-arrow-right ms-2"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Page Actions</h6>
                    </div>
                    <div class="card-body">
                        @auth
                            <a href="{{ route('pages.edit', $page->id) }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="bi bi-pencil me-2"></i>Edit Page
                            </a>
                            <a href="{{ route('chapter.pages.create', [$book->slug, $chapter->slug]) }}"
                                class="btn btn-outline-success w-100 mb-2">
                                <i class="bi bi-file-plus me-2"></i>Add New Page
                            </a>
                            <hr>
                            <button class="btn btn-outline-danger w-100"
                                onclick="deletePage('{{ $page->id }}', '{{ $page->name }}')">
                                <i class="bi bi-trash me-2"></i>Delete Page
                            </button>
                        @endauth
                    </div>
                </div>

                <!-- Chapter Pages -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Pages in {{ $chapter->name }}</h6>
                    </div>
                    <div class="card-body">
                        @if ($chapter->pages->count() > 0)
                            @foreach ($chapter->pages as $chapterPage)
                                <div
                                    class="d-flex align-items-center py-2 {{ $chapterPage->id === $page->id ? 'bg-light rounded px-2' : '' }}">
                                    <i class="bi bi-file-text text-success me-2"></i>
                                    <div class="flex-grow-1">
                                        @if ($chapterPage->id === $page->id)
                                            <strong>{{ $chapterPage->name }}</strong>
                                            @if ($chapterPage->draft)
                                                <span class="badge bg-warning text-dark ms-1"
                                                    style="font-size: 0.7em;">Draft</span>
                                            @endif
                                        @else
                                            <a href="{{ route('chapter.pages.public.show', [$book->slug, $chapter->slug, $chapterPage->slug]) }}"
                                                class="text-decoration-none">
                                                {{ $chapterPage->name }}
                                            </a>
                                            @if ($chapterPage->draft)
                                                <span class="badge bg-warning text-dark ms-1"
                                                    style="font-size: 0.7em;">Draft</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No other pages in this chapter.</p>
                        @endif
                    </div>
                </div>

                <!-- Page Info -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Page Information</h6>
                    </div>
                    <div class="card-body">
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
            </div>
        </div>
    </div>

    @auth
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
            function deletePage(id, name) {
                document.getElementById('pageName').textContent = name;
                document.getElementById('deletePageForm').action = '/pages/' + id;
                new bootstrap.Modal(document.getElementById('deletePageModal')).show();
            }
        </script>
    @endauth

    <style>
        .page-content {
            line-height: 1.7;
        }

        .page-content h1,
        .page-content h2,
        .page-content h3,
        .page-content h4,
        .page-content h5,
        .page-content h6 {
            margin-top: 2rem;
            margin-bottom: 1rem;
        }

        .page-content h1:first-child,
        .page-content h2:first-child,
        .page-content h3:first-child {
            margin-top: 0;
        }

        .page-content img {
            max-width: 100%;
            height: auto;
            border-radius: 0.375rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        }

        .page-content code {
            background-color: #f8f9fa;
            padding: 0.2em 0.4em;
            border-radius: 0.25rem;
            font-size: 0.875em;
        }

        .page-content pre {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.375rem;
            overflow-x: auto;
        }

        .page-content blockquote {
            border-left: 4px solid #dee2e6;
            padding-left: 1rem;
            margin: 1rem 0;
            color: #6c757d;
        }

        .page-content table {
            width: 100%;
            margin-bottom: 1rem;
            border-collapse: collapse;
        }

        .page-content table th,
        .page-content table td {
            padding: 0.75rem;
            border: 1px solid #dee2e6;
        }

        .page-content table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
    </style>
@endsection
