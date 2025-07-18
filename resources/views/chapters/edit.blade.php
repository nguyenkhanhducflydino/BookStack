@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Chapter: {{ $chapter->name }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('chapters.update', $chapter->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Book Selection -->
                            <div class="mb-3">
                                <label for="book_id" class="form-label">Book</label>
                                <select class="form-select @error('book_id') is-invalid @enderror" name="book_id" required>
                                    <option value="">Select a book</option>
                                    @foreach ($books as $book)
                                        <option value="{{ $book->id }}"
                                            {{ old('book_id', $chapter->book_id) == $book->id ? 'selected' : '' }}>
                                            {{ $book->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('book_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Chapter Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Chapter Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name', $chapter->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description', $chapter->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Priority (for ordering) -->
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority (for ordering)</label>
                                <input type="number" class="form-control @error('priority') is-invalid @enderror"
                                    name="priority" value="{{ old('priority', $chapter->priority ?? 0) }}" min="0">
                                <small class="text-muted">Higher numbers appear first</small>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Chapter Image -->
                            <div class="mb-3">
                                <label for="image" class="form-label">Chapter Image</label>
                                @if ($chapter->image)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($chapter->image) }}" alt="{{ $chapter->name }}"
                                            class="img-thumbnail" style="max-width: 200px;">
                                        <div class="form-check mt-2">
                                            <input type="checkbox" class="form-check-input" name="remove_image"
                                                value="1">
                                            <label class="form-check-label">Remove current image</label>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                    name="image" accept="image/*">
                                <small class="text-muted">Upload a new image to replace the current one</small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('chapters.public.show', [$chapter->book->slug, $chapter->slug]) }}"
                                    class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-2"></i>Update Chapter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Chapter Information</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Created:</strong> {{ $chapter->created_at->format('M d, Y') }}</p>
                        <p><strong>Last Updated:</strong> {{ $chapter->updated_at->format('M d, Y') }}</p>
                        @if ($chapter->createdBy)
                            <p><strong>Created By:</strong> {{ $chapter->createdBy->name }}</p>
                        @endif
                        @if ($chapter->updatedBy)
                            <p><strong>Updated By:</strong> {{ $chapter->updatedBy->name }}</p>
                        @endif
                        <p><strong>Pages:</strong> {{ $chapter->pages->count() }}</p>
                        <p><strong>Slug:</strong> <code>{{ $chapter->slug }}</code></p>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Danger Zone</h6>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-outline-danger w-100"
                            onclick="deleteChapter('{{ $chapter->id }}', '{{ $chapter->name }}')">
                            <i class="bi bi-trash me-2"></i>Delete Chapter
                        </button>
                        <small class="text-muted mt-2 d-block">This will delete the chapter and all its pages
                            permanently.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <script>
        function deleteChapter(id, name) {
            document.getElementById('chapterName').textContent = name;
            document.getElementById('deleteChapterForm').action = '/chapters/' + id;
            new bootstrap.Modal(document.getElementById('deleteChapterModal')).show();
        }
    </script>
@endsection
