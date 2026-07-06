<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ListingStatus;
use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Moderation actions on listings, accounts and reports. Guarded as a group by
 * the `admin` middleware.
 */
class ModerationController extends Controller
{
    public function hideListing(Listing $listing): RedirectResponse
    {
        $listing->update(['status' => ListingStatus::Hidden]);

        return back()->with('success', 'Ogłoszenie zostało ukryte.');
    }

    public function restoreListing(Listing $listing): RedirectResponse
    {
        $listing->update([
            'status' => ListingStatus::Active,
            'published_at' => $listing->published_at ?? now(),
        ]);

        return back()->with('success', 'Ogłoszenie zostało przywrócone.');
    }

    public function deleteListing(Listing $listing): RedirectResponse
    {
        // Drop reports pointing at this listing first (polymorphic — no DB cascade).
        $listing->reports()->delete();
        $listing->deleteWithImages();

        return back()->with('success', 'Ogłoszenie zostało usunięte.');
    }

    public function blockUser(Request $request, User $user): RedirectResponse
    {
        abort_if($user->id === $request->user()->id, 403);
        abort_if($user->isAdmin(), 403);

        // Set directly: blocked_at is deliberately not mass-assignable.
        $user->blocked_at = now();
        $user->save();

        return back()->with('success', 'Konto zostało zablokowane.');
    }

    public function unblockUser(User $user): RedirectResponse
    {
        $user->blocked_at = null;
        $user->save();

        return back()->with('success', 'Konto zostało odblokowane.');
    }

    public function resolveReport(Report $report): RedirectResponse
    {
        $report->update(['status' => ReportStatus::Resolved]);

        return back()->with('success', 'Zgłoszenie oznaczono jako rozpatrzone.');
    }

    public function dismissReport(Report $report): RedirectResponse
    {
        $report->update(['status' => ReportStatus::Dismissed]);

        return back()->with('success', 'Zgłoszenie zostało odrzucone.');
    }
}
