<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '../../components/AdminLayout.vue';
import SellerBadge from '../../components/SellerBadge.vue';
import Pagination from '../../components/Pagination.vue';
import EmptyState from '../../components/EmptyState.vue';

const props = defineProps({
    pendingReports: { type: Number, default: 0 },
    users: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const q = ref(props.filters.q);
const type = ref(props.filters.type);
const state = ref(props.filters.state);
let timer;

function apply() {
    router.get('/admin/uzytkownicy', {
        q: q.value || undefined,
        type: type.value || undefined,
        state: state.value || undefined,
    }, { preserveState: true, preserveScroll: true, replace: true });
}

watch(q, () => {
    clearTimeout(timer);
    timer = setTimeout(apply, 350);
});
watch([type, state], apply);

const opts = { preserveScroll: true, preserveState: true };

function block(user) {
    if (confirm(`Zablokować konto „${user.name}"? Użytkownik nie zaloguje się, a jego ogłoszenia znikną z serwisu.`)) {
        router.patch(`/admin/uzytkownicy/${user.id}/zablokuj`, {}, opts);
    }
}
const unblock = (id) => router.patch(`/admin/uzytkownicy/${id}/odblokuj`, {}, opts);
</script>

<template>
    <Head title="Użytkownicy — moderacja" />

    <AdminLayout :pending="pendingReports" title="Użytkownicy">
        <div class="filters">
            <input v-model="q" type="search" class="filters__search" placeholder="Szukaj po nazwie lub e-mailu…" aria-label="Szukaj użytkowników" />
            <select v-model="type" class="filters__select" aria-label="Filtruj po typie konta">
                <option value="">Wszystkie typy</option>
                <option value="prywatna">Osoby prywatne</option>
                <option value="firma">Firmy</option>
            </select>
            <select v-model="state" class="filters__select" aria-label="Filtruj po stanie konta">
                <option value="">Wszystkie konta</option>
                <option value="active">Aktywne</option>
                <option value="blocked">Zablokowane</option>
            </select>
        </div>

        <div v-if="users.data.length" class="table">
            <article v-for="user in users.data" :key="user.id" class="item" :class="{ 'item--blocked': user.isBlocked }">
                <div class="item__body">
                    <div class="item__idline">
                        <Link :href="user.profileUrl" class="item__name">{{ user.companyName || user.name }}</Link>
                        <SellerBadge :type="user.type" />
                        <span v-if="user.isAdmin" class="chip chip--admin">Admin</span>
                        <span v-if="user.isBlocked" class="chip chip--blocked">Zablokowane</span>
                    </div>
                    <p class="item__email">{{ user.email }}</p>
                    <p class="item__meta">
                        {{ user.listingsCount }} ogł. · {{ user.reportsCount }} zgł. przeciw · od {{ user.memberSince }}
                    </p>
                </div>

                <div class="item__actions">
                    <template v-if="!user.isAdmin">
                        <button v-if="!user.isBlocked" type="button" class="act act--danger" @click="block(user)">Zablokuj</button>
                        <button v-else type="button" class="act act--neutral" @click="unblock(user.id)">Odblokuj</button>
                    </template>
                    <span v-else class="item__protected">Konto chronione</span>
                </div>
            </article>
        </div>

        <EmptyState v-else title="Brak użytkowników" text="Żadne konto nie pasuje do filtrów." />

        <Pagination v-if="users.data.length" class="pages" :links="users.links" />
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
    flex: 1 1 240px;
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
    justify-content: space-between;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: 0.9rem 1.1rem;
    flex-wrap: wrap;
}
.item--blocked {
    border-color: color-mix(in srgb, var(--status-hidden) 40%, var(--line));
    background: color-mix(in srgb, var(--status-hidden) 5%, var(--surface));
}
.item__body {
    flex: 1 1 260px;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}
.item__idline {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    flex-wrap: wrap;
}
.item__name {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 1.05rem;
    color: var(--text);
    text-decoration: none;
}
.item__name:hover {
    color: var(--accent-text);
}
.chip {
    font-family: var(--font-mono);
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 0.16rem 0.45rem;
    border-radius: 4px;
}
.chip--admin {
    color: var(--seller-firma);
    background: color-mix(in srgb, var(--seller-firma) 14%, transparent);
}
.chip--blocked {
    color: var(--status-hidden);
    background: color-mix(in srgb, var(--status-hidden) 14%, transparent);
}
.item__email {
    font-family: var(--font-mono);
    font-size: 0.8rem;
    color: var(--text-soft);
    margin: 0;
}
.item__meta {
    font-size: 0.82rem;
    color: var(--text-soft);
    margin: 0;
}
.item__actions {
    display: flex;
    gap: 0.5rem;
    flex: 0 0 auto;
    align-items: center;
}
.item__protected {
    font-family: var(--font-mono);
    font-size: 0.72rem;
    color: var(--text-soft);
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
