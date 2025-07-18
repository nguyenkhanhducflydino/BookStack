<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Page extends Model
{
    use HasSlug, LogsActivity;

    protected $fillable = [
        'name',
        'content',
        'content_html',
        'slug',
        'sort',
        'book_id',
        'chapter_id',
        'template',
        'draft',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'sort' => 'integer',
        'template' => 'boolean',
        'draft' => 'boolean',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'content', 'sort', 'draft'])
            ->logOnlyDirty();
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Convert markdown to HTML
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = $value;
        $this->attributes['content_html'] = $this->markdownToHtml($value);
    }

    protected function markdownToHtml($markdown): string
    {
        $converter = new \League\CommonMark\CommonMarkConverter();
        return $converter->convert($markdown)->getContent();
    }
}
