<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'url',
        'image_url',
        'published_at',
        'source_id',
        'author_id',
        'category_id',
    ];

    /**
     * The attributes that will be hidden.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'source_id',
        'author_id',
        'category_id',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the source that owns the article.
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * Get the author that owns the article.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * The category that owns the article.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Articles by user preferences.
     *
     * @param Builder $query
     * @param User $user
     * @return Builder
     */
    public function scopeByUserPreferences(Builder $query, User $user): Builder
    {
        $query->where(function ($query) use ($user) {
            $query->whereIn('source_id', $user->preferences->pluck('source_id')->filter())
                ->orWhereIn('author_id', $user->preferences->pluck('author_id')->filter())
                ->orWhereIn('category_id', $user->preferences->pluck('category_id')->filter());
        });

        return $query;
    }
}
