<?php

namespace App\Http\Controllers;

use App\Enums\AccountType;
use App\Enums\ListingStatus;
use App\Models\Category;
use App\Models\Listing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Home', [
            'categories' => $this->topCategories(),
            'listings' => $this->latestListings(),
            'stats' => $this->stats(),
        ]);
    }

    /**
     * Top-level categories with a live count of active listings in their subtree.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function topCategories(): Collection
    {
        // One grouped query: active-listing count per (leaf) category.
        $activePerCategory = Listing::query()
            ->where('status', ListingStatus::Active)
            ->selectRaw('category_id, COUNT(*) as aggregate')
            ->groupBy('category_id')
            ->pluck('aggregate', 'category_id');

        return Category::query()
            ->whereNull('parent_id')
            ->with('children:id,parent_id')
            ->orderBy('position')
            ->get()
            ->map(function (Category $category) use ($activePerCategory): array {
                $subtreeIds = $category->children->pluck('id')->push($category->id);

                return [
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'count' => (int) $subtreeIds->sum(fn (int $id): int => (int) ($activePerCategory[$id] ?? 0)),
                ];
            });
    }

    /**
     * The six most recently published listings, shaped for ListingCard.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function latestListings(): Collection
    {
        return Listing::query()
            ->published()
            ->with(['user:id,name,account_type', 'category:id,name', 'images'])
            ->latest('published_at')
            ->take(6)
            ->get()
            ->map(fn (Listing $listing): array => [
                'id' => $listing->id,
                'title' => $listing->title,
                'category' => $listing->category->name,
                'price' => $this->formatPrice($listing->price),
                'location' => $listing->location,
                'postedAt' => $listing->published_at?->locale('pl')->diffForHumans(),
                'sellerName' => $listing->user->name,
                'sellerType' => $listing->user->account_type === AccountType::Company ? 'firma' : 'prywatna',
                'code' => 'OGL-'.$listing->id,
                'image' => $this->imageUrl($listing->images->first()?->path),
            ]);
    }

    /**
     * @return array{listings: string, cities: string}
     */
    private function stats(): array
    {
        return [
            'listings' => number_format(Listing::query()->published()->count(), 0, ',', ' '),
            'cities' => (string) Listing::query()->published()->distinct()->count('location'),
        ];
    }

    private function formatPrice(?string $price): string
    {
        if ($price === null) {
            return 'do uzgodnienia';
        }

        $value = (float) $price;
        $decimals = fmod($value, 1.0) === 0.0 ? 0 : 2;

        return number_format($value, $decimals, ',', ' ').' zł';
    }

    private function imageUrl(?string $path): ?string
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
