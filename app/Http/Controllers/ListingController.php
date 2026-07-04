<?php

namespace App\Http\Controllers;

use App\Enums\AccountType;
use App\Enums\ListingStatus;
use App\Models\Category;
use App\Models\Listing;
use App\Support\ListingCardPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ListingController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'exists:categories,slug'],
            'seller' => ['nullable', Rule::in(['firma', 'prywatna'])],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', Rule::in(['newest', 'price_asc', 'price_desc'])],
        ]);

        $sort = $filters['sort'] ?? 'newest';

        $listings = Listing::query()
            ->published()
            ->with(['user:id,name,account_type', 'category:id,name', 'images'])
            ->when($filters['q'] ?? null, function ($query, string $term): void {
                $query->where(function ($q) use ($term): void {
                    $q->where('title', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->when($filters['category'] ?? null, function ($query, string $slug): void {
                // A top-level pick includes its whole subtree; listings live on leaves.
                $category = Category::query()->where('slug', $slug)->with('children:id,parent_id')->first();

                if ($category) {
                    $query->whereIn('category_id', $category->children->pluck('id')->push($category->id));
                }
            })
            ->when($filters['seller'] ?? null, function ($query, string $seller): void {
                $type = $seller === 'firma' ? AccountType::Company : AccountType::Private;
                $query->whereHas('user', fn ($q) => $q->where('account_type', $type));
            })
            ->when($filters['price_min'] ?? null, fn ($query, $min) => $query->where('price', '>=', $min))
            ->when($filters['price_max'] ?? null, fn ($query, $max) => $query->where('price', '<=', $max))
            ->tap(fn ($query) => $this->applySort($query, $sort))
            ->paginate(12)
            ->withQueryString()
            ->through(ListingCardPresenter::present(...));

        return Inertia::render('Listings/Index', [
            'listings' => $listings,
            'categories' => Category::query()
                ->whereNull('parent_id')
                ->orderBy('position')
                ->get(['name', 'slug']),
            'filters' => [
                'q' => $filters['q'] ?? '',
                'category' => $filters['category'] ?? '',
                'seller' => $filters['seller'] ?? '',
                'price_min' => $filters['price_min'] ?? '',
                'price_max' => $filters['price_max'] ?? '',
                'sort' => $sort,
            ],
        ]);
    }

    public function show(Listing $listing): Response
    {
        // Only active listings are public (see docs/zalozenia-projektowe.md, §5).
        abort_unless($listing->status === ListingStatus::Active, 404);

        $listing->load(['user.companyProfile', 'category.parent', 'images']);

        return Inertia::render('Listings/Show', [
            'listing' => $this->presentDetail($listing),
            'seller' => $this->presentSeller($listing),
            'related' => $this->relatedListings($listing),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function presentDetail(Listing $listing): array
    {
        $category = $listing->category;

        return [
            'id' => $listing->id,
            'title' => $listing->title,
            'description' => $listing->description,
            'price' => ListingCardPresenter::formatPrice($listing->price),
            'isNegotiable' => $listing->is_negotiable,
            'location' => $listing->location,
            'postedAt' => $listing->published_at?->locale('pl')->diffForHumans(),
            'postedAtFull' => $listing->published_at?->locale('pl')->isoFormat('D MMMM YYYY, HH:mm'),
            'code' => 'OGL-'.$listing->id,
            'images' => $listing->images->map(fn ($image) => ListingCardPresenter::imageUrl($image->path))->all(),
            'category' => [
                'name' => $category?->name,
                'slug' => $category?->slug,
                'parent' => $category?->parent ? [
                    'name' => $category->parent->name,
                    'slug' => $category->parent->slug,
                ] : null,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentSeller(Listing $listing): array
    {
        $user = $listing->user;

        return [
            'name' => $user->name,
            'type' => $user->isCompany() ? 'firma' : 'prywatna',
            'memberSince' => $user->created_at?->locale('pl')->isoFormat('MMMM YYYY'),
            'company' => $user->isCompany() && $user->companyProfile ? [
                'name' => $user->companyProfile->company_name,
                'city' => $user->companyProfile->city,
                'taxId' => $user->companyProfile->tax_id,
            ] : null,
        ];
    }

    /**
     * Up to four other active listings from the same category.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function relatedListings(Listing $listing): Collection
    {
        return Listing::query()
            ->published()
            ->where('category_id', $listing->category_id)
            ->whereKeyNot($listing->id)
            ->with(['user:id,name,account_type', 'category:id,name', 'images'])
            ->latest('published_at')
            ->take(4)
            ->get()
            ->map(ListingCardPresenter::present(...));
    }

    /**
     * @param  Builder<Listing>  $query
     */
    private function applySort($query, string $sort): void
    {
        match ($sort) {
            // `price is null` sorts unpriced listings ("do uzgodnienia") last either way.
            'price_asc' => $query->orderByRaw('price is null')->orderBy('price'),
            'price_desc' => $query->orderByRaw('price is null')->orderByDesc('price'),
            default => $query->latest('published_at'),
        };
    }
}
