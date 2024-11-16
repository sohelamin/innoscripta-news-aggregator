<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'bio',
    ];

    /**
     * Get the articles for the author.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
