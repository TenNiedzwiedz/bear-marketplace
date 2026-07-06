<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountType;
use App\Enums\ListingStatus;
use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use App\Support\ListingCardPresenter;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The moderation panel (/admin). Every route in this controller sits behind the
 * `admin` middleware, so no per-action authorization is repeated here.
 */
class DashboardController extends Controller
{
    public function overview(): Response
    {
        return Inertia::render('admin/Overview', [
            ...$this->shared(),
            'stats' => [
                'pendingReports' => Report::query()->where('status', ReportStatus::Pending)->count(),
                'listings' => Listing::query()->count(),
                'activeListings' => Listing::query()->where('status', ListingStatus::Active)->count(),
                'hiddenListings' => Listing::query()->where('status', ListingStatus::Hidden)->count(),
                'users' => User::query()->count(),
                'blockedUsers' => User::query()->whereNotNull('blocked_at')->count(),
            ],
            'recentReports' => Report::query()
                ->where('status', ReportStatus::Pending)
                ->with($this->reportRelations())
                ->latest()
                ->take(6)
                ->get()
                ->map($this->presentReport(...)),
        ]);
    }

    public function reports(Request $request): Response
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::enum(ReportStatus::class)],
        ]);
        $status = $filters['status'] ?? ReportStatus::Pending->value;

        $reports = Report::query()
            ->where('status', $status)
            ->with($this->reportRelations())
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through($this->presentReport(...));

        return Inertia::render('admin/Reports', [
            ...$this->shared(),
            'reports' => $reports,
            'filters' => ['status' => $status],
        ]);
    }

    public function listings(Request $request): Response
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(ListingStatus::class)],
        ]);

        $listings = Listing::query()
            ->with(['user:id,name', 'category:id,name', 'images'])
            ->withCount('reports')
            ->when($filters['q'] ?? null, fn ($q, $term) => $q->where('title', 'like', "%{$term}%"))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Listing $listing) => [
                'id' => $listing->id,
                'title' => $listing->title,
                'status' => $listing->status->value,
                'price' => ListingCardPresenter::formatPrice($listing->price),
                'category' => $listing->category?->name,
                'image' => ListingCardPresenter::imageUrl($listing->images->first()?->path),
                'ownerName' => $listing->user?->name,
                'ownerUrl' => $listing->user ? "/sprzedawca/{$listing->user->id}" : null,
                'reportsCount' => $listing->reports_count,
                'postedAt' => $listing->created_at?->locale('pl')->isoFormat('D MMM YYYY'),
                'url' => "/ogloszenia/{$listing->id}",
            ]);

        return Inertia::render('admin/Listings', [
            ...$this->shared(),
            'listings' => $listings,
            'filters' => [
                'q' => $filters['q'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
        ]);
    }

    public function users(Request $request): Response
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::in(['firma', 'prywatna'])],
            'state' => ['nullable', Rule::in(['blocked', 'active'])],
        ]);

        $users = User::query()
            ->with('companyProfile:id,user_id,company_name')
            ->withCount(['listings', 'reports'])
            ->when($filters['q'] ?? null, function ($query, string $term): void {
                $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%"));
            })
            ->when($filters['type'] ?? null, function ($query, string $type): void {
                $accountType = $type === 'firma' ? AccountType::Company : AccountType::Private;
                $query->where('account_type', $accountType);
            })
            ->when($filters['state'] ?? null, fn ($q, $state) => $state === 'blocked'
                ? $q->whereNotNull('blocked_at')
                : $q->whereNull('blocked_at'))
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'type' => $user->isCompany() ? 'firma' : 'prywatna',
                'companyName' => $user->companyProfile?->company_name,
                'isAdmin' => $user->isAdmin(),
                'isBlocked' => $user->isBlocked(),
                'listingsCount' => $user->listings_count,
                'reportsCount' => $user->reports_count,
                'memberSince' => $user->created_at?->locale('pl')->isoFormat('D MMM YYYY'),
                'profileUrl' => "/sprzedawca/{$user->id}",
            ]);

        return Inertia::render('admin/Users', [
            ...$this->shared(),
            'users' => $users,
            'filters' => [
                'q' => $filters['q'] ?? '',
                'type' => $filters['type'] ?? '',
                'state' => $filters['state'] ?? '',
            ],
        ]);
    }

    /**
     * Props every admin page needs (the sidebar badge).
     *
     * @return array<string, mixed>
     */
    private function shared(): array
    {
        return [
            'pendingReports' => Report::query()->where('status', ReportStatus::Pending)->count(),
        ];
    }

    /**
     * Eager-load the reporter and the polymorphic subject (a listing with its
     * owner, or a user) in one place.
     *
     * @return array<int|string, mixed>
     */
    private function reportRelations(): array
    {
        return [
            'reporter:id,name',
            'reportable' => fn (MorphTo $morphTo) => $morphTo->morphWith([
                Listing::class => ['user:id,name'],
            ]),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentReport(Report $report): array
    {
        return [
            'id' => $report->id,
            'reason' => $report->reason->label(),
            'description' => $report->description,
            'status' => $report->status->value,
            'reporterName' => $report->reporter?->name ?? 'Konto usunięte',
            'createdAt' => $report->created_at?->locale('pl')->diffForHumans(),
            'target' => $this->presentTarget($report),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentTarget(Report $report): array
    {
        $subject = $report->reportable;

        if ($subject === null) {
            return ['type' => $report->reportable_type, 'label' => 'Usunięto', 'gone' => true];
        }

        if ($subject instanceof Listing) {
            return [
                'type' => 'listing',
                'id' => $subject->id,
                'label' => $subject->title,
                'status' => $subject->status->value,
                'ownerName' => $subject->user?->name,
                'url' => "/ogloszenia/{$subject->id}",
            ];
        }

        return [
            'type' => 'user',
            'id' => $subject->id,
            'label' => $subject->name,
            'email' => $subject->email,
            'blocked' => $subject->isBlocked(),
            'url' => "/sprzedawca/{$subject->id}",
        ];
    }
}
