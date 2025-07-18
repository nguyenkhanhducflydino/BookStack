@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="bi bi-collection me-2"></i>Books</h1>
                    <a href="{{ route('books.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Book
                    </a>
                </div>

                @if ($books->count() > 0)
                    <div class="row">
                        @foreach ($books as $book)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    @if ($book->image)
                                        <img src="{{ Storage::url($book->image) }}" class="card-img-top"
                                            alt="{{ $book->name }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-primary text-white d-flex align-items-center justify-content-center"
                                            style="height: 200px;">
                                            <i class="bi bi-book" style="font-size: 4rem;"></i>
                                        </div>
                                    @endif

                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">{{ $book->name }}</h5>
                                        @if ($book->description)
                                            <p class="card-text text-muted">{{ Str::limit($book->description, 100) }}</p>
                                        @endif

                                        <div class="mt-auto">
                                            <small class="text-muted">
                                                Updated {{ $book->updated_at->diffForHumans() }}
                                                @if ($book->createdBy)
                                                    by {{ $book->createdBy->name }}
                                                @endif
                                            </small>

                                            <div class="btn-group w-100 mt-2" role="group">
                                                <a href="{{ route('books.show', $book->slug) }}"
                                                    class="btn btn-outline-primary">
                                                    <i class="bi bi-eye me-1"></i>View
                                                </a>
                                                <a href="{{ route('books.edit', $book->slug) }}"
                                                    class="btn btn-outline-secondary">
                                                    <i class="bi bi-pencil me-1"></i>Edit
                                                </a>
                                                <button class="btn btn-outline-danger"
                                                    onclick="deleteBook('{{ $book->slug }}', '{{ $book->name }}')">
                                                    <i class="bi bi-trash me-1"></i>Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $books->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-collection text-muted" style="font-size: 5rem;"></i>
                        <h3 class="mt-3 text-muted">No books yet</h3>
                        <p class="text-muted">Create your first book to get started.</p>
                        <a href="{{ route('books.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create First Book
                        </a>
                    </div>
                @endif
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
                    <p class="text-danger">This action cannot be undone and will delete all chapters and pages in this book.
                    </p>
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
