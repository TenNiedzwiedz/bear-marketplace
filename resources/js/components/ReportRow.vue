<script setup>
import { Link } from '@inertiajs/vue3';
import ReportStatusPill from './ReportStatusPill.vue';
import StatusPill from './StatusPill.vue';

/**
 * ReportRow — one report in the moderation queue: what was reported, why, by
 * whom, and its status. Pass moderation buttons through the #actions slot.
 */
defineProps({
    report: { type: Object, required: true },
});
</script>

<template>
    <article class="row">
        <div class="row__main">
            <div class="row__top">
                <span class="row__kind" :class="`row__kind--${report.target.type}`">
                    {{ report.target.type === 'listing' ? 'Ogłoszenie' : 'Konto' }}
                </span>
                <ReportStatusPill :status="report.status" />
                <span class="row__reason">{{ report.reason }}</span>
            </div>

            <p class="row__target">
                <Link v-if="report.target.url && !report.target.gone" :href="report.target.url" class="row__link">
                    {{ report.target.label }}
                </Link>
                <span v-else class="row__gone">{{ report.target.label }}</span>

                <StatusPill v-if="report.target.type === 'listing' && report.target.status" :status="report.target.status" />
                <span v-if="report.target.type === 'user' && report.target.blocked" class="row__flag">zablokowane</span>
            </p>

            <p v-if="report.target.ownerName" class="row__owner">Wystawiający: {{ report.target.ownerName }}</p>
            <p v-if="report.target.email" class="row__owner">{{ report.target.email }}</p>

            <p v-if="report.description" class="row__desc">„{{ report.description }}"</p>

            <p class="row__meta">
                Zgłosił(a): {{ report.reporterName }} · {{ report.createdAt }}
            </p>
        </div>

        <div class="row__actions">
            <slot name="actions" />
        </div>
    </article>
</template>

<style scoped>
.row {
    display: flex;
    gap: 1rem;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: 1.1rem 1.2rem;
}
.row__main {
    min-width: 0;
    flex: 1 1 320px;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.row__top {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    flex-wrap: wrap;
}
.row__kind {
    font-family: var(--font-mono);
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    color: var(--text-soft);
    background: color-mix(in srgb, var(--text-soft) 12%, transparent);
}
.row__kind--user {
    color: var(--seller-firma);
    background: color-mix(in srgb, var(--seller-firma) 14%, transparent);
}
.row__reason {
    font-weight: 600;
    font-size: 0.95rem;
}
.row__target {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    flex-wrap: wrap;
    margin: 0;
    font-family: var(--font-display);
    font-size: 1.05rem;
    font-weight: 600;
}
.row__link {
    color: var(--text);
    text-decoration: none;
}
.row__link:hover {
    color: var(--accent-text);
    text-decoration: underline;
}
.row__gone {
    color: var(--text-soft);
    font-style: italic;
}
.row__flag {
    font-family: var(--font-mono);
    font-size: 0.64rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--status-hidden);
}
.row__owner {
    font-size: 0.85rem;
    color: var(--text-soft);
    margin: 0;
}
.row__desc {
    font-size: 0.9rem;
    color: var(--text);
    margin: 0.2rem 0 0;
    line-height: 1.5;
}
.row__meta {
    font-family: var(--font-mono);
    font-size: 0.72rem;
    color: var(--text-soft);
    margin: 0.2rem 0 0;
}
.row__actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    align-items: stretch;
    flex: 0 0 auto;
}
@media (max-width: 620px) {
    .row__actions {
        flex-direction: row;
        flex-wrap: wrap;
    }
}
</style>
