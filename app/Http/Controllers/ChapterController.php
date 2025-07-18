<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChapterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chapters = Chapter::with(['book', 'createdBy'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return view('chapters.index', compact('chapters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Book $book = null)
    {
        if (!$book && $request->has('book_id')) {
            $book = Book::findOrFail($request->book_id);
        }

        return view('chapters.create', compact('book'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'book_id' => 'required|exists:books,id',
        ]);

        $data = $request->only(['name', 'description', 'book_id']);
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        $data['sort'] = Chapter::where('book_id', $request->book_id)->max('sort') + 1;

        $chapter = Chapter::create($data);
        $book = Book::find($request->book_id);

        return redirect()->route('books.show', $book->slug)
            ->with('success', 'Chapter created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book, Chapter $chapter)
    {
        $chapter->load(['pages', 'book']);

        return view('chapters.show', compact('book', 'chapter'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chapter $chapter)
    {
        $chapter->load('book');
        return view('chapters.edit', compact('chapter'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chapter $chapter)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data = $request->only(['name', 'description']);
        $data['updated_by'] = Auth::id();

        $chapter->update($data);

        return redirect()->route('books.show', $chapter->book->slug)
            ->with('success', 'Chapter updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chapter $chapter)
    {
        $book = $chapter->book;
        $chapter->delete();

        return redirect()->route('books.show', $book->slug)
            ->with('success', 'Chapter deleted successfully!');
    }
}
