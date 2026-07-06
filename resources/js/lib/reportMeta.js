/**
 * Report metadata — mirrors App\Enums\ReportReason and App\Enums\ReportStatus.
 * The reason options drive the report form; the status map colours the queue.
 */
export const REPORT_REASONS = [
    { value: 'spam', label: 'Spam lub duplikat' },
    { value: 'prohibited', label: 'Zakazany przedmiot lub treść' },
    { value: 'fraud', label: 'Oszustwo lub wyłudzenie' },
    { value: 'offensive', label: 'Treści obraźliwe' },
    { value: 'other', label: 'Inny powód' },
];

export const REPORT_STATUS_META = {
    pending: { label: 'Oczekuje', color: '--status-draft' },
    resolved: { label: 'Rozpatrzone', color: '--status-active' },
    dismissed: { label: 'Odrzucone', color: '--status-ended' },
};

/** Tab order for the moderation queue. */
export const REPORT_STATUS_ORDER = ['pending', 'resolved', 'dismissed'];
