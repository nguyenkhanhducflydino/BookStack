<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
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
        $pages = Page::with(['book', 'chapter', 'createdBy'])
            ->where('draft', false)
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return view('pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Book $book = null, Chapter $chapter = null)
    {
        if (!$book && $request->has('book_id')) {
            $book = Book::findOrFail($request->book_id);
        }

        if (!$chapter && $request->has('chapter_id')) {
            $chapter = Chapter::findOrFail($request->chapter_id);
        }

        return view('pages.create', compact('book', 'chapter'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'nullable|string',
            'book_id' => 'required|exists:books,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'draft' => 'boolean',
        ]);

        $data = $request->only(['name', 'content', 'book_id', 'chapter_id']);
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        $data['draft'] = $request->has('draft') ? true : false;

        // Set sort order
        if ($request->chapter_id) {
            $data['sort'] = Page::where('chapter_id', $request->chapter_id)->max('sort') + 1;
        } else {
            $data['sort'] = Page::where('book_id', $request->book_id)
                ->whereNull('chapter_id')
                ->max('sort') + 1;
        }

        $page = Page::create($data);
        $book = Book::find($request->book_id);

        if ($request->chapter_id) {
            $chapter = Chapter::find($request->chapter_id);
            return redirect()->route('chapter.pages.public.show', [$book->slug, $chapter->slug, $page->slug])
                ->with('success', 'Page created successfully!');
        }

        return redirect()->route('pages.public.show', [$book->slug, $page->slug])
            ->with('success', 'Page created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book, $chapter_or_page, $page = null)
    {
        if ($page) {
            // URL pattern: /books/{book}/chapters/{chapter}/pages/{page}
            $chapter = Chapter::where('slug', $chapter_or_page)
                ->where('book_id', $book->id)
                ->firstOrFail();

            $page = Page::where('slug', $page)
                ->where('book_id', $book->id)
                ->where('chapter_id', $chapter->id)
                ->firstOrFail();

            $page->load(['book', 'chapter']);

            // Get previous and next pages in the same chapter
            $previousPage = Page::where('chapter_id', $chapter->id)
                ->where('id', '<', $page->id)
                ->orderBy('id', 'desc')
                ->first();

            $nextPage = Page::where('chapter_id', $chapter->id)
                ->where('id', '>', $page->id)
                ->orderBy('id', 'asc')
                ->first();
        } else {
            // URL pattern: /books/{book}/pages/{page}
            // Here $chapter_or_page is actually the page slug
            $page = Page::where('slug', $chapter_or_page)
                ->where('book_id', $book->id)
                ->first();

            if (!$page) {
                abort(404, 'Page not found');
            }

            // If page belongs to a chapter, redirect to the proper chapter/page URL
            if ($page->chapter_id) {
                $chapter = Chapter::find($page->chapter_id);
                return redirect()->route('chapter.pages.public.show', [
                    $book->slug,
                    $chapter->slug,
                    $page->slug
                ]);
            }

            $page->load(['book']);
            $chapter = null;

            // Get previous and next pages in the same book (not in chapters)
            $previousPage = Page::where('book_id', $book->id)
                ->whereNull('chapter_id')
                ->where('id', '<', $page->id)
                ->orderBy('id', 'desc')
                ->first();

            $nextPage = Page::where('book_id', $book->id)
                ->whereNull('chapter_id')
                ->where('id', '>', $page->id)
                ->orderBy('id', 'asc')
                ->first();
        }

        return view('pages.show', compact('book', 'chapter', 'page', 'previousPage', 'nextPage'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page)
    {
        $page->load(['book', 'chapter']);
        return view('pages.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Page $page)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'nullable|string',
            'draft' => 'boolean',
        ]);

        $data = $request->only(['name', 'content']);
        $data['updated_by'] = Auth::id();
        $data['draft'] = $request->has('draft') ? true : false;

        $page->update($data);

        if ($page->chapter_id) {
            return redirect()->route('chapter.pages.public.show', [$page->book->slug, $page->chapter->slug, $page->slug])
                ->with('success', 'Page updated successfully!');
        }

        return redirect()->route('pages.public.show', [$page->book->slug, $page->slug])
            ->with('success', 'Page updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        $book = $page->book;
        $chapter = $page->chapter;
        $page->delete();

        if ($chapter) {
            return redirect()->route('chapters.public.show', [$book->slug, $chapter->slug])
                ->with('success', 'Page deleted successfully!');
        }

        return redirect()->route('books.public.show', $book->slug)
            ->with('success', 'Page deleted successfully!');
    }
}
