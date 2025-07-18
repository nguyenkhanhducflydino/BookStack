@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            <i class="bi bi-plus-circle me-2"></i>Create New Chapter
                            @if ($book)
                                in "{{ $book->name }}"
                            @endif
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('chapters.store') }}" method="POST">
                            @csrf

                            @if ($book)
                                <input type="hidden" name="book_id" value="{{ $book->id }}">
                            @else
                                <div class="mb-3">
                                    <label for="book_id" class="form-label">Select Book *</label>
                                    <select class="form-select @error('book_id') is-invalid @enderror" id="book_id"
                                        name="book_id" required>
                                        <option value="">Choose a book...</option>
                                        @foreach (App\Models\Book::orderBy('name')->get() as $bookOption)
                                            <option value="{{ $bookOption->id }}"
                                                {{ old('book_id') == $bookOption->id ? 'selected' : '' }}>
                                                {{ $bookOption->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('book_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="name" class="form-label">Chapter Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="4">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                @if ($book)
                                    <a href="{{ route('books.show', $book->slug) }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>Back to Book
                                    </a>
                                @else
                                    <a href="{{ route('books.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>Back to Books
                                    </a>
                                @endif
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Create Chapter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
