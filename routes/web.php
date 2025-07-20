<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;

// Home and Search routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Authentication routes
Auth::routes();

// Protected routes
Route::middleware('auth')->group(function () {

    // Books routes (admin CRUD)
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

    Route::get('/books/{book}/create-chapter', [ChapterController::class, 'create'])
        ->name('chapters.create');
    Route::get('/books/{book}/create-page', [PageController::class, 'create'])
        ->name('pages.create');

    // Chapters routes
    Route::resource('chapters', ChapterController::class)->except(['create', 'show']);
    Route::get('/books/{book}/chapters/{chapter}/create-page', [PageController::class, 'create'])->name('chapter.pages.create');

    // Pages routes
    Route::resource('pages', PageController::class)->except(['create', 'show']);
});

// Public view routes (no auth required)
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.public.show');
Route::get('/books/{book}/chapters/{chapter}', [ChapterController::class, 'show'])->name('chapters.public.show');
Route::get('/books/{book}/pages/{page}', [PageController::class, 'show'])->name('pages.public.show');
Route::get('/books/{book}/chapters/{chapter}/pages/{page}', [PageController::class, 'show'])->name('chapter.pages.public.show');

// Add these authenticated show routes
Route::middleware('auth')->group(function () {
    Route::get('/admin/books/{book}', [BookController::class, 'show'])->name('books.show');
    Route::get('/admin/books/{book}/chapters/{chapter}', [ChapterController::class, 'show'])->name('chapters.show');
    Route::get('/admin/books/{book}/pages/{page}', [PageController::class, 'show'])->name('pages.show');
    Route::get('/admin/books/{book}/chapters/{chapter}/pages/{page}', [PageController::class, 'show'])->name('chapter.pages.show');

    // Payment routes
    Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
    Route::post('/payment/create-intent', [PaymentController::class, 'createPaymentIntent'])->name('payment.create-intent');
    Route::get('/payment/success', [PaymentController::class, 'handleSuccess'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'handleCancel'])->name('payment.cancel');
    Route::get('/payment/history', [PaymentController::class, 'history'])->name('payment.history');
});

// Webhook route (no auth required)
Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');
