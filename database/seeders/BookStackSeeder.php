<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BookStackSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@bookstack.local',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Create sample user
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'user@bookstack.local',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Create sample books
        $book1 = Book::create([
            'name' => 'Laravel Documentation',
            'description' => 'Complete guide to Laravel framework development',
            'slug' => 'laravel-documentation',
            'sort' => 1,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $book2 = Book::create([
            'name' => 'Project Management Guide',
            'description' => 'Best practices for managing software projects',
            'slug' => 'project-management-guide',
            'sort' => 2,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // Create chapters for Laravel Documentation
        $chapter1 = Chapter::create([
            'name' => 'Getting Started',
            'description' => 'Basic setup and installation',
            'slug' => 'getting-started',
            'sort' => 1,
            'book_id' => $book1->id,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $chapter2 = Chapter::create([
            'name' => 'Routing',
            'description' => 'How to define and manage routes',
            'slug' => 'routing',
            'sort' => 2,
            'book_id' => $book1->id,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        // Create pages
        Page::create([
            'name' => 'Installation',
            'content' => '# Installation

Laravel can be installed via Composer:

```bash
composer create-project laravel/laravel my-project
```

## Requirements
- PHP >= 8.2
- Composer
- Node.js & NPM

## Configuration
After installation, configure your `.env` file with database credentials.',
            'slug' => 'installation',
            'sort' => 1,
            'book_id' => $book1->id,
            'chapter_id' => $chapter1->id,
            'draft' => false,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        Page::create([
            'name' => 'Directory Structure',
            'content' => '# Directory Structure

Laravel follows a specific directory structure:

- `app/` - Application logic
- `config/` - Configuration files
- `database/` - Migrations and seeders
- `public/` - Web accessible files
- `resources/` - Views and assets
- `routes/` - Route definitions

Each directory serves a specific purpose in the Laravel ecosystem.',
            'slug' => 'directory-structure',
            'sort' => 2,
            'book_id' => $book1->id,
            'chapter_id' => $chapter1->id,
            'draft' => false,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        Page::create([
            'name' => 'Basic Routing',
            'content' => '# Basic Routing

Define routes in `routes/web.php`:

```php
Route::get(\'/\', function () {
    return view(\'welcome\');
});

Route::get(\'/users/{id}\', function ($id) {
    return \'User \'.$id;
});
```

## Route Parameters
Routes can accept parameters for dynamic content.',
            'slug' => 'basic-routing',
            'sort' => 1,
            'book_id' => $book1->id,
            'chapter_id' => $chapter2->id,
            'draft' => false,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        // Pages without chapters (directly in book)
        Page::create([
            'name' => 'Welcome to Laravel',
            'content' => '# Welcome to Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling.

## Why Laravel?
- Elegant syntax
- Powerful features
- Great documentation
- Active community',
            'slug' => 'welcome-to-laravel',
            'sort' => 1,
            'book_id' => $book1->id,
            'chapter_id' => null,
            'draft' => false,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        // Create pages for Project Management book
        Page::create([
            'name' => 'Agile Methodology',
            'content' => '# Agile Methodology

Agile is a project management approach that emphasizes:

- **Individuals and interactions** over processes and tools
- **Working software** over comprehensive documentation
- **Customer collaboration** over contract negotiation
- **Responding to change** over following a plan

## Scrum Framework
Scrum is the most popular agile framework with roles, events, and artifacts.',
            'slug' => 'agile-methodology',
            'sort' => 1,
            'book_id' => $book2->id,
            'chapter_id' => null,
            'draft' => false,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }
}
