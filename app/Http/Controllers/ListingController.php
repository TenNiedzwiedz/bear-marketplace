<?php

namespace App\Http\Controllers;

use App\Enums\AccountType;
use App\Enums\ListingStatus;
use App\Models\Category;
use App\Models\Listing;
use App\Support\ListingCardPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

    public function create(): Response
    {
        return Inertia::render('Listings/Form', [
            'listing' => null,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedListing($request);
        $publish = $data['action'] === 'publish';

        $listing = $request->user()->listings()->create([
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price'],
            'currency' => 'PLN',
            'is_negotiable' => $request->boolean('is_negotiable'),
            'location' => $data['location'],
            'status' => $publish ? ListingStatus::Active : ListingStatus::Draft,
            'published_at' => $publish ? now() : null,
        ]);

        $this->storeImages($listing, $request);

        return $publish
            ? redirect()->route('listings.show', $listing)
            : redirect()->route('panel.listings');
    }

    public function edit(Listing $listing): Response
    {
        Gate::authorize('update', $listing);

        $listing->load('images');

        return Inertia::render('Listings/Form', [
            'listing' => [
                'id' => $listing->id,
                'title' => $listing->title,
                'description' => $listing->description,
                'category_id' => $listing->category_id,
                'price' => $listing->price !== null ? (float) $listing->price : null,
                'is_negotiable' => $listing->is_negotiable,
                'location' => $listing->location,
                'images' => $listing->images->map(fn ($image) => [
                    'id' => $image->id,
                    'url' => ListingCardPresenter::imageUrl($image->path),
                ])->all(),
            ],
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function update(Request $request, Listing $listing): RedirectResponse
    {
        Gate::authorize('update', $listing);

        $data = $this->validatedListing($request);
        $publish = $data['action'] === 'publish';

        $listing->update([
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price'],
            'is_negotiable' => $request->boolean('is_negotiable'),
            'location' => $data['location'],
            'status' => $publish ? ListingStatus::Active : ListingStatus::Draft,
            'published_at' => $publish ? ($listing->published_at ?? now()) : $listing->published_at,
        ]);

        if (! empty($data['remove_images'])) {
            $listing->images()->whereIn('id', $data['remove_images'])->get()->each(function ($image): void {
                if (! Str::startsWith($image->path, ['http://', 'https://'])) {
                    Storage::disk('public')->delete($image->path);
                }
                $image->delete();
            });
        }

        $this->storeImages($listing, $request);

        return $publish
            ? redirect()->route('listings.show', $listing)
            : redirect()->route('panel.listings');
    }

    public function end(Listing $listing): RedirectResponse
    {
        Gate::authorize('update', $listing);
        abort_unless($listing->status === ListingStatus::Active, 403);

        $listing->update(['status' => ListingStatus::Ended]);

        return back();
    }

    public function reactivate(Listing $listing): RedirectResponse
    {
        Gate::authorize('update', $listing);
        abort_unless(in_array($listing->status, [ListingStatus::Ended, ListingStatus::Draft], true), 403);

        $listing->update([
            'status' => ListingStatus::Active,
            'published_at' => $listing->published_at ?? now(),
        ]);

        return back();
    }

    public function destroy(Listing $listing): RedirectResponse
    {
        Gate::authorize('delete', $listing);

        // Remove uploaded files first; DB image rows cascade on listing delete.
        $listing->loadMissing('images')->images->each(function ($image): void {
            if (! Str::startsWith($image->path, ['http://', 'https://'])) {
                Storage::disk('public')->delete($image->path);
            }
        });

        $listing->delete();

        return back();
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedListing(Request $request): array
    {
        // An empty price means "do uzgodnienia", not the number zero.
        $request->merge(['price' => $request->input('price') === '' ? null : $request->input('price')]);

        return $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'category_id' => [
                'required', 'integer', Rule::exists('categories', 'id'),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (Category::whereKey($value)->whereHas('children')->exists()) {
                        $fail('Wybierz konkretną podkategorię.');
                    }
                },
            ],
            'description' => ['required', 'string', 'max:5000'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'is_negotiable' => ['boolean'],
            'location' => ['required', 'string', 'max:120'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer'],
            'action' => ['required', Rule::in(['publish', 'draft'])],
        ]);
    }

    private function storeImages(Listing $listing, Request $request): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $position = (int) ($listing->images()->max('position') ?? -1);

        foreach ($request->file('images') as $file) {
            $listing->images()->create([
                'path' => $file->store('listings', 'public'),
                'position' => ++$position,
            ]);
        }
    }

    /**
     * Categories grouped by top-level parent, offering only leaf options.
     *
     * @return list<array{label: string, options: list<array{value: int, label: string}>}>
     */
    private function categoryOptions(): array
    {
        return Category::query()
            ->whereNull('parent_id')
            ->orderBy('position')
            ->with(['children' => fn ($q) => $q->orderBy('position')])
            ->get()
            ->map(fn (Category $parent) => [
                'label' => $parent->name,
                'options' => $parent->children->map(fn (Category $child) => [
                    'value' => $child->id,
                    'label' => $child->name,
                ])->all(),
            ])
            ->filter(fn (array $group) => count($group['options']) > 0)
            ->values()
            ->all();
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
