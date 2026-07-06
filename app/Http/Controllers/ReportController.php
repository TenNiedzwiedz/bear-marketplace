<?php

namespace App\Http\Controllers;

use App\Enums\ReportReason;
use App\Enums\ReportStatus;
use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    /**
     * File a report against a listing or a user account. Reachable by any signed
     * -in user; self-reports are rejected and duplicates are quietly de-duped.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reportable_type' => ['required', Rule::in(['listing', 'user'])],
            'reportable_id' => ['required', 'integer'],
            'reason' => ['required', Rule::enum(ReportReason::class)],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $subject = $this->resolveSubject($data['reportable_type'], (int) $data['reportable_id']);
        $reporter = $request->user();

        // You cannot report your own listing or your own account.
        abort_if($this->ownerId($subject) === $reporter->id, 403);

        $alreadyReported = $subject->reports()
            ->where('user_id', $reporter->id)
            ->where('status', ReportStatus::Pending)
            ->exists();

        if ($alreadyReported) {
            return back()->with('success', 'To zgłoszenie już do nas trafiło — dziękujemy.');
        }

        $subject->reports()->create([
            'user_id' => $reporter->id,
            'reason' => $data['reason'],
            'description' => $data['description'] ?? null,
            'status' => ReportStatus::Pending,
        ]);

        return back()->with('success', 'Dziękujemy. Zgłoszenie trafiło do moderacji.');
    }

    private function resolveSubject(string $type, int $id): Listing|User
    {
        return $type === 'listing'
            ? Listing::findOrFail($id)
            : User::findOrFail($id);
    }

    private function ownerId(Model $subject): int
    {
        return $subject instanceof Listing ? $subject->user_id : $subject->id;
    }
}
