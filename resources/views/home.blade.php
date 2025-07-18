@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <!-- Welcome Header -->
                <div class="jumbotron bg-light p-5 rounded mb-4">
                    <div class="text-center">
                        <h1 class="display-4">
                            <i class="bi bi-book text-primary me-3"></i>
                            Welcome to BookStack Clone
                        </h1>
                        <p class="lead">A modern wiki and documentation platform built with Laravel</p>
                        @guest
                            <a class="btn btn-primary btn-lg me-2" href="{{ route('register') }}" role="button">
                                <i class="bi bi-person-plus me-2"></i>Get Started
                            </a>
                            <a class="btn btn-outline-primary btn-lg" href="{{ route('login') }}" role="button">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </a>
                        @else
                            <a class="btn btn-primary btn-lg" href="{{ route('books.create') }}" role="button">
                                <i class="bi bi-plus-circle me-2"></i>Create Your First Book
                            </a>
                        @endguest
                    </div>
                </div>

                <div class="row">
                    <!-- Recent Books -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-collection me-2"></i>Recent Books
                                </h5>
                                @auth
                                    <a href="{{ route('books.index') }}" class="btn btn-sm btn-outline-primary">
                                        View All
                                    </a>
                                @endauth
                            </div>
                            <div class="card-body">
                                @if ($recentBooks->count() > 0)
                                    @foreach ($recentBooks as $book)
                                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                            <div class="me-3">
                                                @if ($book->image)
                                                    <img src="{{ Storage::url($book->image) }}" alt="{{ $book->name }}"
                                                        class="rounded" width="50" height="50">
                                                @else
                                                    <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center"
                                                        style="width: 50px; height: 50px;">
                                                        <i class="bi bi-book"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <a href="{{ route('books.public.show', $book->slug) }}"
                                                        class="text-decoration-none">
                                                        {{ $book->name }}
                                                    </a>
                                                </h6>
                                                <small class="text-muted">
                                                    Updated {{ $book->updated_at->diffForHumans() }}
                                                    @if ($book->createdBy)
                                                        by {{ $book->createdBy->name }}
                                                    @endif
                                                </small>
                                                @if ($book->description)
                                                    <p class="mb-0 small text-muted mt-1">
                                                        {{ Str::limit($book->description, 100) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <i class="bi bi-collection text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2">No books available yet</p>
                                        @auth
                                            <a href="{{ route('books.create') }}" class="btn btn-primary">
                                                <i class="bi bi-plus-circle me-2"></i>Create First Book
                                            </a>
                                        @endauth
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Recent Pages -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-file-text me-2"></i>Recent Pages
                                </h5>
                            </div>
                            <div class="card-body">
                                @if ($recentPages->count() > 0)
                                    @foreach ($recentPages as $page)
                                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                            <div class="me-3">
                                                <div class="bg-success text-white rounded d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <i class="bi bi-file-text"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <a href="{{ route('pages.public.show', [$page->book->slug, $page->slug]) }}"
                                                        class="text-decoration-none">
                                                        {{ $page->name }}
                                                    </a>
                                                </h6>
                                                <small class="text-muted">
                                                    in <a href="{{ route('books.public.show', $page->book->slug) }}"
                                                        class="text-decoration-none">{{ $page->book->name }}</a>
                                                </small><br>
                                                <small class="text-muted">
                                                    Updated {{ $page->updated_at->diffForHumans() }}
                                                    @if ($page->createdBy)
                                                        by {{ $page->createdBy->name }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <i class="bi bi-file-text text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2">No pages available yet</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
