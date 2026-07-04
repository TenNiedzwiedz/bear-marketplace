<?php

namespace App\Support;

use App\Enums\AccountType;
use App\Models\Listing;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Shapes a Listing into the props ListingCard expects. Shared by every view
 * that renders listing cards so price, seller and image formatting stay in one
 * place. Expects `user`, `category` and `images` to be eager-loaded.
 */
class ListingCardPresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function present(Listing $listing): array
    {
        return [
            'id' => $listing->id,
            'title' => $listing->title,
            'category' => $listing->category?->name,
            'price' => self::formatPrice($listing->price),
            'location' => $listing->location,
            'postedAt' => $listing->published_at?->locale('pl')->diffForHumans(),
            'sellerName' => $listing->user?->name,
            'sellerType' => $listing->user?->account_type === AccountType::Company ? 'firma' : 'prywatna',
            'code' => 'OGL-'.$listing->id,
            'image' => self::imageUrl($listing->images->first()?->path),
        ];
    }

    private static function formatPrice(?string $price): string
    {
        if ($price === null) {
            return 'do uzgodnienia';
        }

        $value = (float) $price;
        $decimals = fmod($value, 1.0) === 0.0 ? 0 : 2;

        return number_format($value, $decimals, ',', ' ').' zł';
    }

    private static function imageUrl(?string $path): ?string
    {
        if ($path === null) {
            return null;
        }

        // Seed data stores absolute URLs; uploaded files live on the public disk.
        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : Storage::disk('public')->url($path);
    }
}
