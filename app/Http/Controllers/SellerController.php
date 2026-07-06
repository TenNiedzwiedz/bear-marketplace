<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\User;
use App\Support\ListingCardPresenter;
use Inertia\Inertia;
use Inertia\Response;

class SellerController extends Controller
{
    public function show(User $user): Response
    {
        // A blocked account has no public profile.
        abort_if($user->isBlocked(), 404);

        $user->loadMissing('companyProfile');

        $listings = Listing::query()
            ->published()
            ->where('user_id', $user->id)
            ->with(['user:id,name,account_type', 'category:id,name', 'images'])
            ->latest('published_at')
            ->paginate(12)
            ->through(ListingCardPresenter::present(...));

        return Inertia::render('Sellers/Show', [
            'seller' => $this->presentSeller($user),
            'listings' => $listings,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function presentSeller(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'type' => $user->isCompany() ? 'firma' : 'prywatna',
            'initials' => $this->initials($user->name),
            'memberSince' => $user->created_at?->locale('pl')->isoFormat('MMMM YYYY'),
            'activeCount' => $user->listings()->published()->count(),
            'company' => $user->isCompany() && $user->companyProfile ? [
                'name' => $user->companyProfile->company_name,
                'city' => $user->companyProfile->city,
                'taxId' => $user->companyProfile->tax_id,
            ] : null,
        ];
    }

    private function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $letters = array_map(fn (string $part) => mb_substr($part, 0, 1), array_slice($parts, 0, 2));

        return mb_strtoupper(implode('', $letters));
    }
}
