@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1><i class="bi bi-search me-2"></i>Search Results</h1>

                @if ($query)
                    <p class="text-muted">Results for: <strong>"{{ $query }}"</strong></p>

                    <!-- Books Results -->
                    @if ($books->count() > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-collection me-2"></i>Books ({{ $books->count() }})</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($books as $book)
                                        <div class="col-md-6 mb-3">
                                            <div class="d-flex align-items-center">
                                                @if ($book->image)
                                                    <img src="{{ Storage::url($book->image) }}" alt="{{ $book->name }}"
                                                        class="me-3 rounded"
                                                        style="width: 60px; height: 75px; object-fit: cover;">
                                                @else
                                                    <div class="me-3 bg-primary text-white rounded d-flex align-items-center justify-content-center"
                                                        style="width: 60px; height: 75px;">
                                                        <i class="bi bi-book"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-1">
                                                        <a href="{{ route('books.public.show', $book->slug) }}"
                                                            class="text-decoration-none">
                                                            {{ $book->name }}
                                                        </a>
                                                    </h6>
                                                    @if ($book->description)
                                                        <p class="mb-0 small text-muted">
                                                            {{ Str::limit($book->description, 100) }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Pages Results -->
                    @if ($pages->count() > 0)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Pages ({{ $pages->count() }})</h5>
                            </div>
                            <div class="card-body">
                                @foreach ($pages as $page)
                                    <div class="border-bottom pb-3 mb-3">
                                        <h6 class="mb-1">
                                            <a href="{{ route('pages.public.show', [$page->book->slug, $page->slug]) }}"
                                                class="text-decoration-none">
                                                {{ $page->name }}
                                            </a>
                                        </h6>
                                        <div class="text-muted small mb-2">
                                            in <a href="{{ route('books.public.show', $page->book->slug) }}"
                                                class="text-decoration-none">{{ $page->book->name }}</a>
                                        </div>
                                        @if ($page->content)
                                            <p class="mb-0 small">{{ Str::limit(strip_tags($page->content), 200) }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($books->count() == 0 && $pages->count() == 0)
                        <div class="text-center py-5">
                            <i class="bi bi-search text-muted" style="font-size: 5rem;"></i>
                            <h3 class="mt-3 text-muted">No results found</h3>
                            <p class="text-muted">Try adjusting your search terms or browse all books.</p>
                            <a href="{{ route('books.index') }}" class="btn btn-primary">
                                <i class="bi bi-collection me-2"></i>Browse Books
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-search text-muted" style="font-size: 5rem;"></i>
                        <h3 class="mt-3 text-muted">Enter a search term</h3>
                        <p class="text-muted">Use the search box above to find books and pages.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
