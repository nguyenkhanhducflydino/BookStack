<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Page;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $recentBooks = Book::with('createdBy')
            ->orderBy('updated_at', 'desc')
            ->limit(6)
            ->get();

        $recentPages = Page::with(['book', 'createdBy'])
            ->where('draft', false)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('home', compact('recentBooks', 'recentPages'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) {
            return redirect()->route('home');
        }

        $books = Book::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();

        $pages = Page::where('name', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%")
            ->where('draft', false)
            ->with(['book'])
            ->get();

        return view('search', compact('books', 'pages', 'query'));
    }
}
