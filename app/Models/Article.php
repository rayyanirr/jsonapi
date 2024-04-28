<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'category_id' => 'integer',
        'user_id' => 'string',
    ];

    public $resourceType = 'articles';

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function scopeYear(Builder $query, string $year): void
    {
        $query->whereYear('created_at', $year);
    }

    public function scopeMonth(Builder $query, string $month): void
    {
        $query->whereMonth('created_at', $month);
    }

    public function scopeCategories(Builder $query, $categories): void
    {
        $categorySlugs = explode(',', $categories);
        $query->whereHas('category', function ($q) use ($categorySlugs) {
            $q->whereIn('slug', $categorySlugs);
        });
    }

    public function scopeAuthors(Builder $query, $authors): void
    {
        $authorsNames = explode(',', $authors);
        $query->whereHas('author', function ($q) use ($authorsNames) {
            $q->whereIn('name', $authorsNames);
        });
    }
}
