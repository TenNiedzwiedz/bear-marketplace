<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '../../components/AdminLayout.vue';
import ReportRow from '../../components/ReportRow.vue';
import EmptyState from '../../components/EmptyState.vue';

const props = defineProps({
    pendingReports: { type: Number, default: 0 },
    stats: { type: Object, required: true },
    recentReports: { type: Array, default: () => [] },
});

const cards = computed(() => [
    { label: 'Oczekujące zgłoszenia', value: props.stats.pendingReports, accent: true, href: '/admin/zgloszenia' },
    { label: 'Aktywne ogłoszenia', value: props.stats.activeListings, href: '/admin/ogloszenia?status=active' },
    { label: 'Ukryte ogłoszenia', value: props.stats.hiddenListings, href: '/admin/ogloszenia?status=hidden' },
    { label: 'Wszystkie ogłoszenia', value: props.stats.listings, href: '/admin/ogloszenia' },
    { label: 'Zablokowane konta', value: props.stats.blockedUsers, href: '/admin/uzytkownicy?state=blocked' },
    { label: 'Użytkownicy', value: props.stats.users, href: '/admin/uzytkownicy' },
]);
</script>

<template>
    <Head title="Panel administratora" />

    <AdminLayout :pending="pendingReports" title="Przegląd moderacji">
        <section class="cards">
            <Link v-for="card in cards" :key="card.label" :href="card.href" class="card" :class="{ 'card--accent': card.accent }">
                <span class="card__value">{{ card.value }}</span>
                <span class="card__label">{{ card.label }}</span>
            </Link>
        </section>

        <section class="recent">
            <div class="recent__head">
                <h2 class="recent__title">Najnowsze zgłoszenia</h2>
                <Link href="/admin/zgloszenia" class="recent__all">Cała kolejka →</Link>
            </div>

            <div v-if="recentReports.length" class="recent__list">
                <ReportRow v-for="report in recentReports" :key="report.id" :report="report" />
            </div>
            <EmptyState
                v-else
                title="Kolejka jest pusta"
                text="Żadne zgłoszenie nie czeka na moderację. Dobra robota."
            />
        </section>
    </AdminLayout>
</template>

<style scoped>
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
    gap: 1rem;
    margin-bottom: 2.2rem;
}
.card {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: 1.1rem 1.2rem;
    text-decoration: none;
    transition: border-color 0.15s ease, transform 0.15s ease;
}
.card:hover {
    border-color: var(--line-strong);
    transform: translateY(-2px);
}
.card--accent {
    border-color: color-mix(in srgb, var(--seller-firma) 45%, transparent);
    background: color-mix(in srgb, var(--seller-firma) 8%, var(--surface));
}
.card__value {
    font-family: var(--font-display);
    font-weight: 800;
    font-size: 2rem;
    line-height: 1;
    color: var(--text);
    font-variant-numeric: tabular-nums;
}
.card--accent .card__value {
    color: var(--seller-firma);
}
.card__label {
    font-family: var(--font-mono);
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--text-soft);
}

.recent__head {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
}
.recent__title {
    font-family: var(--font-display);
    font-weight: 700;
    font-size: 1.3rem;
    letter-spacing: -0.02em;
    margin: 0;
}
.recent__all {
    font-family: var(--font-mono);
    font-size: 0.78rem;
    color: var(--accent-text);
    text-decoration: none;
    white-space: nowrap;
}
.recent__all:hover {
    text-decoration: underline;
}
.recent__list {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}
@media (prefers-reduced-motion: reduce) {
    .card {
        transition: none;
    }
    .card:hover {
        transform: none;
    }
}
</style>
