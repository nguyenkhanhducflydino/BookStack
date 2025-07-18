@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="bi bi-pencil me-2"></i>Edit Book: {{ $book->name }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('books.update', $book->slug) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Book Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $book->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="4">{{ old('description', $book->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Cover Image</label>
                                @if ($book->image)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($book->image) }}" alt="Current cover"
                                            class="img-thumbnail" style="max-height: 150px;">
                                        <div class="form-text">Current cover image</div>
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                    id="image" name="image" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Leave empty to keep current image. Supported formats: JPEG, PNG, JPG,
                                    GIF. Max size: 2MB</div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('books.show', $book->slug) }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Book
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Update Book
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
