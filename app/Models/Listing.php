<?php

namespace App\Models;

use App\Enums\ListingStatus;
use Database\Factories\ListingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'category_id',
    'title',
    'description',
    'price',
    'currency',
    'is_negotiable',
    'location',
    'status',
    'published_at',
])]
class Listing extends Model
{
    /** @use HasFactory<ListingFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_negotiable' => 'boolean',
            'status' => ListingStatus::class,
            'published_at' => 'datetime',
        ];
    }

    /**
     * Only listings visible on public pages: active, and owned by an account
     * that is not blocked (a blocked seller's listings drop out of public view).
     *
     * @param  Builder<Listing>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', ListingStatus::Active)
            ->whereHas('user', fn (Builder $q) => $q->whereNull('blocked_at'));
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<ListingImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class)->orderBy('position');
    }

    /**
     * @return MorphMany<Report, $this>
     */
    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    /**
     * Delete the listing along with any uploaded image files. Seed data stores
     * absolute URLs (nothing to remove); uploads live on the public disk.
     */
    public function deleteWithImages(): void
    {
        $this->loadMissing('images')->images->each(function (ListingImage $image): void {
            if (! Str::startsWith($image->path, ['http://', 'https://'])) {
                Storage::disk('public')->delete($image->path);
            }
        });

        $this->delete();
    }
}
