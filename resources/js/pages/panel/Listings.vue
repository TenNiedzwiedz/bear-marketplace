<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PanelLayout from '../../components/PanelLayout.vue';
import AppButton from '../../components/AppButton.vue';
import StatusPill from '../../components/StatusPill.vue';
import Pagination from '../../components/Pagination.vue';
import EmptyState from '../../components/EmptyState.vue';

defineProps({
    user: { type: Object, required: true },
    konto: { type: String, default: null },
    listings: { type: Object, required: true }, // Laravel paginator
});
</script>

<template>
    <Head title="Panel — moje ogłoszenia" />

    <PanelLayout :user="user" :konto="konto" title="Moje ogłoszenia">
        <template #actions>
            <span class="count">{{ listings.total }} łącznie</span>
        </template>

        <div v-if="listings.data.length" class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ogłoszenie</th>
                        <th>Cena</th>
                        <th>Status</th>
                        <th>Dodano</th>
                        <th><span class="visually-hidden">Akcje</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="l in listings.data" :key="l.id">
                        <td>
                            <div class="cell-listing">
                                <span class="thumb" :style="l.image ? { backgroundImage: `url(${l.image})` } : null"></span>
                                <span class="cell-listing__text">
                                    <span class="cell-listing__title">{{ l.title }}</span>
                                    <span class="cell-listing__cat">{{ l.category }}</span>
                                </span>
                            </div>
                        </td>
                        <td class="mono">{{ l.price }}</td>
                        <td><StatusPill :status="l.status" /></td>
                        <td class="mono muted">{{ l.postedAt }}</td>
                        <td class="cell-actions">
                            <div class="cell-actions__inner">
                                <Link v-if="l.isActive" :href="l.url" class="rowlink">Zobacz</Link>
                                <button type="button" class="rowlink">Edytuj</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <EmptyState
            v-else
            title="Nie masz jeszcze ogłoszeń"
            text="Wystaw pierwsze — zajmie to dwie minuty."
        >
            <AppButton href="/wystaw" variant="primary">Wystaw ogłoszenie</AppButton>
        </EmptyState>

        <div v-if="listings.data.length" class="foot">
            <Pagination :links="listings.links" />
        </div>
    </PanelLayout>
</template>

<style scoped>
.count {
    font-family: var(--font-mono);
    font-size: 0.78rem;
    color: var(--text-soft);
}
.mono {
    font-family: var(--font-mono);
    font-variant-numeric: tabular-nums;
}
.muted {
    color: var(--text-soft);
}
.visually-hidden {
    position: absolute;
    width: 1px;
    height: 1px;
    overflow: hidden;
    clip: rect(0 0 0 0);
}

.table-wrap {
    overflow-x: auto;
    border: 1px solid var(--line);
    border-radius: 8px;
}
.table {
    width: 100%;
    min-width: 640px;
    border-collapse: collapse;
    background: var(--surface);
}
.table th {
    text-align: left;
    font-family: var(--font-mono);
    font-size: 0.66rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--text-soft);
    padding: 0.85rem 1rem;
    border-bottom: 1px solid var(--line);
}
.table td {
    padding: 0.85rem 1rem;
    border-bottom: 1px solid var(--line);
    font-size: 0.9rem;
    vertical-align: middle;
}
.table tbody tr:last-child td {
    border-bottom: 0;
}
.table tbody tr:hover {
    background: color-mix(in srgb, var(--accent) 5%, transparent);
}
.cell-listing {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}
.thumb {
    width: 46px;
    height: 40px;
    flex: none;
    border-radius: 5px;
    background: var(--surface-2) center / cover no-repeat;
    border: 1px solid var(--line);
}
.cell-listing__text {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    min-width: 0;
}
.cell-listing__title {
    font-weight: 600;
    line-height: 1.2;
}
.cell-listing__cat {
    font-family: var(--font-mono);
    font-size: 0.68rem;
    color: var(--text-soft);
}
/* The <td> stays a normal table cell (full row height, aligned border); an
   inner flex box lays out the actions. */
.cell-actions {
    text-align: right;
}
.cell-actions__inner {
    display: flex;
    gap: 0.8rem;
    justify-content: flex-end;
    white-space: nowrap;
}
.rowlink {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--accent-text);
    background: none;
    border: 0;
    padding: 0;
    cursor: pointer;
    text-decoration: none;
}
.rowlink:hover {
    text-decoration: underline;
}

.foot {
    margin-top: 1.6rem;
}
</style>
