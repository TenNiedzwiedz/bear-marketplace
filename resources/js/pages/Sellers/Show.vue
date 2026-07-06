<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import SiteHeader from '../../components/SiteHeader.vue';
import SiteFooter from '../../components/SiteFooter.vue';
import SellerBadge from '../../components/SellerBadge.vue';
import ListingCard from '../../components/ListingCard.vue';
import Pagination from '../../components/Pagination.vue';
import EmptyState from '../../components/EmptyState.vue';
import ReportDialog from '../../components/ReportDialog.vue';

const props = defineProps({
    seller: { type: Object, required: true },
    listings: { type: Object, required: true },
});

const page = usePage();
const canReport = computed(() => {
    const me = page.props.auth?.user;
    return !!me && me.id !== props.seller.id;
});

const countLabel = (n) => {
    const mod10 = n % 10;
    const mod100 = n % 100;
    if (n === 1) return 'aktywne ogłoszenie';
    if (mod10 >= 2 && mod10 <= 4 && (mod100 < 10 || mod100 >= 20)) return 'aktywne ogłoszenia';
    return 'aktywnych ogłoszeń';
};
</script>

<template>
    <Head :title="`${seller.name} — sprzedawca`" />

    <SiteHeader />

    <main class="wrap">
        <nav class="crumbs" aria-label="Ścieżka nawigacji">
            <Link href="/ogloszenia">Ogłoszenia</Link>
            <span class="crumbs__sep" aria-hidden="true">›</span>
            <span>{{ seller.name }}</span>
        </nav>

        <header class="profile">
            <div class="profile__avatar" :class="`profile__avatar--${seller.type}`" aria-hidden="true">
                {{ seller.initials }}
            </div>
            <div class="profile__body">
                <SellerBadge :type="seller.type" />
                <h1 class="profile__name">{{ seller.company?.name || seller.name }}</h1>
                <p class="profile__meta">
                    <span>{{ seller.activeCount }} {{ countLabel(seller.activeCount) }}</span>
                    <template v-if="seller.memberSince">
                        <span class="dot">·</span>
                        <span>Na Bear od {{ seller.memberSince }}</span>
                    </template>
                </p>
                <div v-if="canReport" class="profile__report">
                    <ReportDialog type="user" :id="seller.id" :subject="seller.name" label="Zgłoś sprzedawcę" />
                </div>
            </div>

            <dl v-if="seller.company" class="profile__facts">
                <div v-if="seller.company.city" class="profile__fact">
                    <dt>Lokalizacja</dt>
                    <dd>{{ seller.company.city }}</dd>
                </div>
                <div v-if="seller.company.taxId" class="profile__fact">
                    <dt>NIP</dt>
                    <dd>{{ seller.company.taxId }}</dd>
                </div>
            </dl>
        </header>

        <section class="listings">
            <h2 class="section-title">Ogłoszenia sprzedającego</h2>

            <div v-if="listings.data.length" class="listings__grid">
                <ListingCard
                    v-for="l in listings.data"
                    :key="l.id"
                    :category="l.category"
                    :title="l.title"
                    :price="l.price"
                    :location="l.location"
                    :posted-at="l.postedAt"
                    :seller-name="l.sellerName"
                    :seller-type="l.sellerType"
                    :code="l.code"
                    :image="l.image"
                    :href="l.url"
                />
            </div>

            <EmptyState
                v-else
                title="Brak aktywnych ogłoszeń"
                text="Ten sprzedawca nie ma teraz żadnych aktywnych ogłoszeń. Zajrzyj później."
            />

            <Pagination v-if="listings.data.length" class="listings__pages" :links="listings.links" />
        </section>
    </main>

    <SiteFooter />
</template>

<style scoped>
.wrap {
    max-width: 1180px;
    margin: 0 auto;
    padding: clamp(1.4rem, 4vw, 2.6rem) clamp(1rem, 4vw, 2.4rem);
}

/* Breadcrumb */
.crumbs {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
    font-family: var(--font-mono);
    font-size: 0.74rem;
    letter-spacing: 0.04em;
    color: var(--text-soft);
    margin-bottom: 1.4rem;
}
.crumbs a {
    color: var(--text-soft);
    text-decoration: none;
}
.crumbs a:hover {
    color: var(--accent-text);
}
.crumbs__sep {
    opacity: 0.6;
}

/* Profile header */
.profile {
    display: grid;
    grid-template-columns: auto 1fr auto;
    align-items: center;
    gap: clamp(1rem, 3vw, 1.6rem);
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 10px;
    padding: clamp(1.3rem, 3vw, 1.8rem);
    box-shadow: var(--shadow);
}
.profile__avatar {
    width: clamp(56px, 12vw, 76px);
    height: clamp(56px, 12vw, 76px);
    border-radius: 50%;
    display: grid;
    place-items: center;
    font-family: var(--font-display);
    font-weight: 700;
    font-size: clamp(1.3rem, 4vw, 1.8rem);
    color: #fff;
    letter-spacing: 0.02em;
}
.profile__avatar--firma {
    background: var(--seller-firma);
}
.profile__avatar--prywatna {
    background: var(--seller-priv);
}
.profile__body {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    align-items: flex-start;
}
.profile__report {
    margin-top: 0.3rem;
}
.profile__name {
    font-family: var(--font-display);
    font-weight: 800;
    font-size: clamp(1.4rem, 3.5vw, 2.1rem);
    line-height: 1.05;
    letter-spacing: -0.03em;
    margin: 0;
    text-wrap: balance;
}
.profile__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
    font-family: var(--font-mono);
    font-size: 0.78rem;
    color: var(--text-soft);
    margin: 0;
}
.profile__meta .dot {
    opacity: 0.5;
}
.profile__facts {
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
    margin: 0;
    padding-left: clamp(1rem, 3vw, 1.6rem);
    border-left: 1px solid var(--line);
}
.profile__fact {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}
.profile__fact dt {
    font-family: var(--font-mono);
    font-size: 0.64rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--text-soft);
}
.profile__fact dd {
    margin: 0;
    font-weight: 500;
    font-size: 0.92rem;
}

/* Listings */
.listings {
    margin-top: clamp(2rem, 5vw, 3rem);
}
.section-title {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 1.3rem;
    letter-spacing: -0.02em;
    margin: 0 0 1.2rem;
}
.listings__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1.5rem;
}
.listings__pages {
    margin-top: 2rem;
}

@media (max-width: 720px) {
    .profile {
        grid-template-columns: auto 1fr;
    }
    .profile__facts {
        grid-column: 1 / -1;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 1.2rem;
        padding-left: 0;
        padding-top: 1rem;
        border-left: 0;
        border-top: 1px solid var(--line);
    }
}
</style>
