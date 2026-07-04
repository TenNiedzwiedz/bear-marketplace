<?php

namespace App\Models;

use Database\Factories\ListingImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['listing_id', 'path', 'position'])]
class ListingImage extends Model
{
    /** @use HasFactory<ListingImageFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Listing, $this>
     */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }
}
