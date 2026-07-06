<?php

namespace App\Http\Controllers;

use App\Enums\ListingStatus;
use App\Enums\ReportStatus;
use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use App\Support\ListingCardPresenter;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The user panel, split into sections that share a sidebar. Auth is not built
 * yet, so it runs on a demonstration account (the most active user, optionally
 * of a chosen type via ?konto=firma|prywatna) to show both layouts.
 */
class DashboardController extends Controller
{
    public function overview(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('panel/Overview', [
            ...$this->shared($user),
            'overview' => $this->overviewData($user),
        ]);
    }

    public function listings(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('panel/Listings', [
            ...$this->shared($user),
            'listings' => $this->paginatedListings($user),
        ]);
    }

    public function messages(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('panel/Messages', $this->shared($user));
    }

    public function account(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('panel/Account', $this->shared($user));
    }

    /**
     * Props every panel view needs for the shared sidebar.
     *
     * @return array<string, mixed>
     */
    private function shared(User $user): array
    {
        $user->loadMissing('companyProfile');

        return [
            'user' => $this->presentUser($user),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentUser(User $user): array
    {
        $isCompany = $user->isCompany();

        return [
            'name' => $user->name,
            'greetingName' => $isCompany
                ? ($user->companyProfile?->company_name ?? $user->name)
                : $this->firstName($user->name),
            'email' => $user->email,
            'type' => $isCompany ? 'firma' : 'prywatna',
            'memberSince' => $user->created_at?->locale('pl')->isoFormat('MMMM YYYY'),
            'company' => $isCompany && $user->companyProfile ? [
                'name' => $user->companyProfile->company_name,
                'taxId' => $user->companyProfile->tax_id,
                'city' => $user->companyProfile->city,
                'address' => $user->companyProfile->address_line,
            ] : null,
        ];
    }

    /**
     * First name, skipping the academic titles the faker prepends (dr, mgr, …).
     */
    private function firstName(string $name): string
    {
        $titles = ['dr', 'dr hab.', 'mgr', 'inż.', 'prof.', 'lek.', 'gen.'];

        foreach (preg_split('/\s+/', trim($name)) as $part) {
            if (! in_array(mb_strtolower($part), $titles, true) && ! str_ends_with($part, '.')) {
                return $part;
            }
        }

        return $name;
    }

    /**
     * @return array<string, mixed>
     */
    private function overviewData(User $user): array
    {
        $counts = $user->listings()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $inModeration = Report::query()
            ->where('status', ReportStatus::Pending)
            ->whereHasMorph('reportable', Listing::class, fn ($q) => $q->where('user_id', $user->id))
            ->count();

        return [
            'active' => (int) ($counts[ListingStatus::Active->value] ?? 0),
            'draft' => (int) ($counts[ListingStatus::Draft->value] ?? 0),
            'ended' => (int) ($counts[ListingStatus::Ended->value] ?? 0),
            'hidden' => (int) ($counts[ListingStatus::Hidden->value] ?? 0),
            'total' => (int) $counts->sum(),
            'inModeration' => $inModeration,
            'activity' => $this->activitySeries($user),
        ];
    }

    /**
     * New listings per day across the last 14 days (real publish timestamps).
     *
     * @return list<array{label: string, count: int}>
     */
    private function activitySeries(User $user): array
    {
        $since = now()->subDays(13)->startOfDay();

        $byDay = $user->listings()
            ->whereNotNull('published_at')
            ->where('published_at', '>=', $since)
            ->get(['published_at'])
            ->groupBy(fn ($listing) => $listing->published_at->format('Y-m-d'))
            ->map->count();

        return collect(range(0, 13))
            ->map(function (int $offset) use ($since, $byDay): array {
                $day = $since->copy()->addDays($offset);

                return [
                    'label' => $day->locale('pl')->isoFormat('D MMM'),
                    'count' => (int) ($byDay[$day->format('Y-m-d')] ?? 0),
                ];
            })
            ->all();
    }

    private function paginatedListings(User $user): LengthAwarePaginator
    {
        return $user->listings()
            ->with(['category:id,name', 'images'])
            ->latest()
            ->paginate(10)
            ->through(fn ($listing) => [
                'id' => $listing->id,
                'title' => $listing->title,
                'category' => $listing->category?->name,
                'price' => ListingCardPresenter::formatPrice($listing->price),
                'status' => $listing->status->value,
                'image' => ListingCardPresenter::imageUrl($listing->images->first()?->path),
                'postedAt' => $listing->created_at?->locale('pl')->isoFormat('D MMM YYYY'),
                'isActive' => $listing->status === ListingStatus::Active,
                'url' => "/ogloszenia/{$listing->id}",
            ]);
    }
}
