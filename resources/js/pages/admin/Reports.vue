<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '../../components/AdminLayout.vue';
import ReportRow from '../../components/ReportRow.vue';
import Pagination from '../../components/Pagination.vue';
import EmptyState from '../../components/EmptyState.vue';
import { REPORT_STATUS_ORDER, REPORT_STATUS_META } from '../../lib/reportMeta';

defineProps({
    pendingReports: { type: Number, default: 0 },
    reports: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const opts = { preserveScroll: true, preserveState: true };

const resolve = (id) => router.patch(`/admin/zgloszenia/${id}/rozpatrz`, {}, opts);
const dismiss = (id) => router.patch(`/admin/zgloszenia/${id}/odrzuc`, {}, opts);
const hideListing = (id) => router.patch(`/admin/ogloszenia/${id}/ukryj`, {}, opts);
const restoreListing = (id) => router.patch(`/admin/ogloszenia/${id}/przywroc`, {}, opts);

function blockUser(id) {
    if (confirm('Zablokować to konto? Użytkownik nie będzie mógł się zalogować, a jego ogłoszenia znikną z serwisu.')) {
        router.patch(`/admin/uzytkownicy/${id}/zablokuj`, {}, opts);
    }
}
const unblockUser = (id) => router.patch(`/admin/uzytkownicy/${id}/odblokuj`, {}, opts);
</script>

<template>
    <Head title="Zgłoszenia — moderacja" />

    <AdminLayout :pending="pendingReports" title="Zgłoszenia">
        <nav class="tabs" aria-label="Filtr statusu zgłoszeń">
            <Link
                v-for="status in REPORT_STATUS_ORDER"
                :key="status"
                :href="`/admin/zgloszenia?status=${status}`"
                class="tab"
                :class="{ 'is-active': filters.status === status }"
                :aria-current="filters.status === status ? 'page' : undefined"
                preserve-scroll
            >{{ REPORT_STATUS_META[status].label }}</Link>
        </nav>

        <div v-if="reports.data.length" class="list">
            <ReportRow v-for="report in reports.data" :key="report.id" :report="report">
                <template v-if="report.status === 'pending'" #actions>
                    <button type="button" class="act act--resolve" @click="resolve(report.id)">Rozpatrz</button>
                    <button type="button" class="act act--dismiss" @click="dismiss(report.id)">Odrzuć</button>

                    <template v-if="report.target.type === 'listing' && !report.target.gone">
                        <button
                            v-if="report.target.status !== 'hidden'"
                            type="button"
                            class="act act--danger"
                            @click="hideListing(report.target.id)"
                        >Ukryj ogłoszenie</button>
                        <button
                            v-else
                            type="button"
                            class="act act--neutral"
                            @click="restoreListing(report.target.id)"
                        >Przywróć</button>
                    </template>

                    <template v-else-if="report.target.type === 'user' && !report.target.gone">
                        <button
                            v-if="!report.target.blocked"
                            type="button"
                            class="act act--danger"
                            @click="blockUser(report.target.id)"
                        >Zablokuj konto</button>
                        <button
                            v-else
                            type="button"
                            class="act act--neutral"
                            @click="unblockUser(report.target.id)"
                        >Odblokuj</button>
                    </template>
                </template>
            </ReportRow>
        </div>

        <EmptyState
            v-else
            title="Brak zgłoszeń"
            :text="filters.status === 'pending' ? 'Nic nie czeka na moderację.' : 'W tym stanie nie ma zgłoszeń.'"
        />

        <Pagination v-if="reports.data.length" class="pages" :links="reports.links" />
    </AdminLayout>
</template>

<style scoped>
.tabs {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
    margin-bottom: 1.4rem;
}
.tab {
    font-family: var(--font-mono);
    font-size: 0.8rem;
    font-weight: 600;
    padding: 0.45rem 0.9rem;
    border: 1px solid var(--line-strong);
    border-radius: 999px;
    text-decoration: none;
    color: var(--text-soft);
}
.tab:hover {
    color: var(--text);
    border-color: var(--text);
}
.tab.is-active {
    background: var(--seller-firma);
    color: #fff;
    border-color: transparent;
}
.list {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
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
.act--resolve {
    background: var(--status-active);
    color: #fff;
}
.act--dismiss {
    border-color: var(--line-strong);
    color: var(--text);
}
.act--dismiss:hover {
    border-color: var(--text);
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
