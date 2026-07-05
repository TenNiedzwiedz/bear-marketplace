/**
 * Listing status metadata — Polish label and the semantic color token for each
 * ListingStatus. Shared by StatusPill and the dashboard's portfolio bar so a
 * status always reads the same way. Mirrors App\Enums\ListingStatus.
 */
export const STATUS_META = {
    active: { label: 'Aktywne', color: '--status-active' },
    draft: { label: 'Robocze', color: '--status-draft' },
    ended: { label: 'Zakończone', color: '--status-ended' },
    hidden: { label: 'Ukryte', color: '--status-hidden' },
};

/** Reading order for legends and the portfolio bar. */
export const STATUS_ORDER = ['active', 'draft', 'ended', 'hidden'];
