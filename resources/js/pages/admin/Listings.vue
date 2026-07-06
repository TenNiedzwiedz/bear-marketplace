<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '../../components/AdminLayout.vue';
import StatusPill from '../../components/StatusPill.vue';
import Pagination from '../../components/Pagination.vue';
import EmptyState from '../../components/EmptyState.vue';
import { STATUS_META, STATUS_ORDER } from '../../lib/listingStatus';

const props = defineProps({
    pendingReports: { type: Number, default: 0 },
    listings: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const q = ref(props.filters.q);
const status = ref(props.filters.status);
let timer;

function apply() {
    router.get('/admin/ogloszenia', { q: q.value || undefined, status: status.value || undefined }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

watch(q, () => {
    clearTimeout(timer);
    timer = setTimeout(apply, 350);
});
watch(status, apply);

const opts = { preserveScroll: true, preserveState: true };
const hide = (id) => router.patch(`/admin/ogloszenia/${id}/ukryj`, {}, opts);
const restore = (id) => router.patch(`/admin/ogloszenia/${id}/przywroc`, {}, opts);

function destroy(listing) {
    if (confirm(`Usunąć ogłoszenie „${listing.title}"? Tej operacji nie można cofnąć.`)) {
        router.delete(`/admin/ogloszenia/${listing.id}`, opts);
    }
}
</script>

<template>
    <Head title="Ogłoszenia — moderacja" />

    <AdminLayout :pending="pendingReports" title="Ogłoszenia">
        <div class="filters">
            <input v-model="q" type="search" class="filters__search" placeholder="Szukaj po tytule…" aria-label="Szukaj ogłoszeń" />
            <select v-model="status" class="filters__select" aria-label="Filtruj po statusie">
                <option value="">Wszystkie statusy</option>
                <option v-for="s in STATUS_ORDER" :key="s" :value="s">{{ STATUS_META[s].label }}</option>
            </select>
        </div>

        <div v-if="listings.data.length" class="table">
            <article v-for="listing in listings.data" :key="listing.id" class="item">
                <div class="item__thumb" :style="listing.image ? { backgroundImage: `url(${listing.image})` } : null"></div>

                <div class="item__body">
                    <Link :href="listing.url" class="item__title">{{ listing.title }}</Link>
                    <p class="item__meta">
                        {{ listing.category || 'Bez kategorii' }} ·
                        <Link v-if="listing.ownerUrl" :href="listing.ownerUrl" class="item__owner">{{ listing.ownerName }}</Link>
                        <span v-else>{{ listing.ownerName }}</span>
                        · {{ listing.postedAt }}
                    </p>
                    <div class="item__tags">
                        <StatusPill :status="listing.status" />
                        <span class="item__price">{{ listing.price }}</span>
                        <Link
                            v-if="listing.reportsCount > 0"
                            href="/admin/zgloszenia"
                            class="item__reports"
                        >{{ listing.reportsCount }} zgł.</Link>
                    </div>
                </div>

                <div class="item__actions">
                    <button v-if="listing.status !== 'hidden'" type="button" class="act act--danger" @click="hide(listing.id)">Ukryj</button>
                    <button v-else type="button" class="act act--neutral" @click="restore(listing.id)">Przywróć</button>
                    <button type="button" class="act act--danger" @click="destroy(listing)">Usuń</button>
                </div>
            </article>
        </div>

        <EmptyState v-else title="Brak ogłoszeń" text="Żadne ogłoszenie nie pasuje do filtrów." />

        <Pagination v-if="listings.data.length" class="pages" :links="listings.links" />
    </AdminLayout>
</template>

<style scoped>
.filters {
    display: flex;
    gap: 0.7rem;
    flex-wrap: wrap;
    margin-bottom: 1.4rem;
}
.filters__search,
.filters__select {
    font-family: var(--font-sans);
    font-size: 0.92rem;
    color: var(--text);
    background: var(--surface);
    border: 1.5px solid var(--line-strong);
    border-radius: 6px;
    padding: 0.55rem 0.75rem;
}
.filters__search {
    flex: 1 1 260px;
}
.filters__search:focus,
.filters__select:focus {
    outline: none;
    border-color: var(--accent);
}
.table {
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
}
.item {
    display: flex;
    gap: 1rem;
    align-items: center;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: 0.8rem 1rem;
    flex-wrap: wrap;
}
.item__thumb {
    flex: none;
    width: 64px;
    height: 64px;
    border-radius: 6px;
    background-color: var(--surface-2);
    background-size: cover;
    background-position: center;
    border: 1px solid var(--line);
}
.item__body {
    flex: 1 1 240px;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}
.item__title {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 1.05rem;
    color: var(--text);
    text-decoration: none;
}
.item__title:hover {
    color: var(--accent-text);
}
.item__meta {
    font-size: 0.82rem;
    color: var(--text-soft);
    margin: 0;
}
.item__owner {
    color: var(--text-soft);
    text-decoration: underline;
}
.item__owner:hover {
    color: var(--text);
}
.item__tags {
    display: flex;
    align-items: center;
    gap: 0.7rem;
    flex-wrap: wrap;
}
.item__price {
    font-family: var(--font-mono);
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text);
}
.item__reports {
    font-family: var(--font-mono);
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--status-hidden);
    text-decoration: none;
    padding: 0.15rem 0.45rem;
    border-radius: 999px;
    background: color-mix(in srgb, var(--status-hidden) 12%, transparent);
}
.item__actions {
    display: flex;
    gap: 0.5rem;
    flex: 0 0 auto;
    flex-wrap: wrap;
}
.act {
    font-family: var(--font-sans);
    font-size: 0.82rem;
    font-weight: 600;
    padding: 0.5rem 0.8rem;
    border-radius: 5px;
    border: 1.5px solid transparent;
    cursor: pointer;
    white-space: nowrap;
    background: transparent;
}
.act--danger {
    border-color: color-mix(in srgb, var(--status-hidden) 55%, transparent);
    color: var(--status-hidden);
}
.act--danger:hover {
    background: color-mix(in srgb, var(--status-hidden) 12%, transparent);
}
.act--neutral {
    border-color: var(--line-strong);
    color: var(--text-soft);
}
.act--neutral:hover {
    color: var(--text);
    border-color: var(--text);
}
.pages {
    margin-top: 1.6rem;
}
</style>
