@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <!-- Book Header -->
                <div class="d-flex align-items-start mb-4">
                    @if ($book->image)
                        <img src="{{ Storage::url($book->image) }}" alt="{{ $book->name }}" class="me-4 rounded"
                            style="width: 120px; height: 150px; object-fit: cover;">
                    @else
                        <div class="me-4 bg-primary text-white rounded d-flex align-items-center justify-content-center"
                            style="width: 120px; height: 150px;">
                            <i class="bi bi-book" style="font-size: 3rem;"></i>
                        </div>
                    @endif

                    <div class="flex-grow-1">
                        <h1>{{ $book->name }}</h1>
                        @if ($book->description)
                            <p class="text-muted">{{ $book->description }}</p>
                        @endif
                        <div class="text-muted">
                            <small>
                                Created {{ $book->created_at->diffForHumans() }}
                                @if ($book->createdBy)
                                    by {{ $book->createdBy->name }}
                                @endif
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Book Content -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Contents</h5>
                        @auth
                            <div class="btn-group" role="group">
                                <a href="{{ route('chapters.create', $book->slug) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-plus me-1"></i>Add Chapter
                                </a>
                                <a href="{{ route('pages.create', $book->slug) }}" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-file-plus me-1"></i>Add Page
                                </a>
                            </div>
                        @endauth
                    </div>
                    <div class="card-body">
                        @if ($book->chapters->count() > 0 || $book->pages->count() > 0)

                            <!-- Pages without chapters -->
                            @if ($book->pages->count() > 0)
                                @foreach ($book->pages as $page)
                                    <div class="d-flex align-items-center py-2 border-bottom">
                                        <i class="bi bi-file-text text-success me-3"></i>
                                        <div class="flex-grow-1">
                                            <a href="{{ route('pages.show', [$book->slug, $page->slug]) }}"
                                                class="text-decoration-none fw-medium">
                                                {{ $page->name }}
                                            </a>
                                        </div>
                                        <div class="text-muted small">
                                            {{ $page->updated_at->diffForHumans() }}
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <!-- Chapters with pages -->
                            @foreach ($book->chapters as $chapter)
                                <div class="mt-3">
                                    <div class="d-flex align-items-center py-2 bg-light rounded">
                                        <i class="bi bi-collection text-primary me-3 ms-2"></i>
                                        <div class="flex-grow-1">
                                            <a href="{{ route('chapters.show', [$book->slug, $chapter->slug]) }}"
                                                class="text-decoration-none fw-bold">
                                                {{ $chapter->name }}
                                            </a>
                                            @if ($chapter->description)
                                                <div class="text-muted small">{{ $chapter->description }}</div>
                                            @endif
                                        </div>
                                        <div class="text-muted small me-2">
                                            {{ $chapter->updated_at->diffForHumans() }}
                                        </div>
                                    </div>

                                    @if ($chapter->pages->count() > 0)
                                        @foreach ($chapter->pages as $page)
                                            <div class="d-flex align-items-center py-2 ps-5 border-bottom">
                                                <i class="bi bi-file-text text-success me-3"></i>
                                                <div class="flex-grow-1">
                                                    <a href="{{ route('chapter.pages.show', [$book->slug, $chapter->slug, $page->slug]) }}"
                                                        class="text-decoration-none">
                                                        {{ $page->name }}
                                                    </a>
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $page->updated_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-files text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3 text-muted">No content yet</h5>
                                <p class="text-muted">Start by creating a chapter or page.</p>
                                @auth
                                    <div class="mt-3">
                                        <a href="{{ route('chapters.create', $book->slug) }}" class="btn btn-primary me-2">
                                            <i class="bi bi-plus me-1"></i>Create Chapter
                                        </a>
                                        <a href="{{ route('pages.create', $book->slug) }}" class="btn btn-success">
                                            <i class="bi bi-file-plus me-1"></i>Create Page
                                        </a>
                                    </div>
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
                        <h6 class="mb-0">Book Actions</h6>
                    </div>
                    <div class="card-body">
                        @auth
                            <a href="{{ route('books.edit', $book->slug) }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="bi bi-pencil me-2"></i>Edit Book
                            </a>
                            <a href="{{ route('chapters.create', $book->slug) }}" class="btn btn-outline-secondary w-100 mb-2">
                                <i class="bi bi-plus me-2"></i>Add Chapter
                            </a>
                            <a href="{{ route('pages.create', $book->slug) }}" class="btn btn-outline-success w-100 mb-2">
                                <i class="bi bi-file-plus me-2"></i>Add Page
                            </a>
                            <hr>
                            <button class="btn btn-outline-danger w-100"
                                onclick="deleteBook('{{ $book->slug }}', '{{ $book->name }}')">
                                <i class="bi bi-trash me-2"></i>Delete Book
                            </button>
                        @endauth
                    </div>
                </div>

                <!-- Book Stats -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="h4 text-primary">{{ $book->chapters->count() }}</div>
                                <small class="text-muted">Chapters</small>
                            </div>
                            <div class="col-4">
                                <div class="h4 text-success">
                                    {{ $book->pages->count() +$book->chapters->sum(function ($chapter) {return $chapter->pages->count();}) }}
                                </div>
                                <small class="text-muted">Pages</small>
                            </div>
                            <div class="col-4">
                                <div class="h4 text-info">{{ $book->updated_at->diffForHumans() }}</div>
                                <small class="text-muted">Updated</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="bookName"></strong>?</p>
                    <p class="text-danger">This action cannot be undone and will delete all chapters and pages in this
                        book.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteBook(slug, name) {
            document.getElementById('bookName').textContent = name;
            document.getElementById('deleteForm').action = '/books/' + slug;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
@endsection
