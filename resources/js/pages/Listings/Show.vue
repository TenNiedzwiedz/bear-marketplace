<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import SiteHeader from '../../components/SiteHeader.vue';
import SiteFooter from '../../components/SiteFooter.vue';
import SellerBadge from '../../components/SellerBadge.vue';
import AppButton from '../../components/AppButton.vue';
import AppTag from '../../components/AppTag.vue';
import BearSeal from '../../components/BearSeal.vue';
import SectionBlock from '../../components/SectionBlock.vue';
import ListingCard from '../../components/ListingCard.vue';

const props = defineProps({
    listing: { type: Object, required: true },
    seller: { type: Object, required: true },
    related: { type: Array, default: () => [] },
});

const active = ref(0);
const hasImages = computed(() => props.listing.images.length > 0);
</script>

<template>
    <Head :title="listing.title" />

    <SiteHeader />

    <main class="wrap">
        <nav class="crumbs" aria-label="Ścieżka nawigacji">
            <Link href="/ogloszenia">Ogłoszenia</Link>
            <span class="crumbs__sep" aria-hidden="true">›</span>
            <template v-if="listing.category.parent">
                <Link :href="`/ogloszenia?category=${listing.category.parent.slug}`">{{ listing.category.parent.name }}</Link>
                <span class="crumbs__sep" aria-hidden="true">›</span>
            </template>
            <Link :href="`/ogloszenia?category=${listing.category.slug}`">{{ listing.category.name }}</Link>
        </nav>

        <header class="head">
            <h1 class="head__title">{{ listing.title }}</h1>
            <p class="head__meta">
                <span>{{ listing.location }}</span>
                <span class="dot">·</span>
                <span>{{ listing.postedAt }}</span>
                <span class="dot">·</span>
                <span class="head__code">{{ listing.code }}</span>
            </p>
        </header>

        <div class="detail">
            <!-- ===================== LEFT: gallery + description ===================== -->
            <div class="detail__main">
                <div class="gallery">
                    <div class="gallery__main">
                        <img v-if="hasImages" :src="listing.images[active]" :alt="listing.title" />
                        <div v-else class="gallery__placeholder">
                            <BearSeal :size="96" :ring="false" />
                        </div>
                    </div>
                    <div v-if="listing.images.length > 1" class="gallery__thumbs">
                        <button
                            v-for="(img, i) in listing.images"
                            :key="i"
                            type="button"
                            class="gallery__thumb"
                            :class="{ 'is-active': i === active }"
                            :aria-label="`Zdjęcie ${i + 1}`"
                            :aria-current="i === active ? 'true' : undefined"
                            @click="active = i"
                        >
                            <img :src="img" :alt="`Zdjęcie ${i + 1}`" />
                        </button>
                    </div>
                </div>

                <section class="desc">
                    <h2 class="section-title">Opis</h2>
                    <div class="desc__body">{{ listing.description }}</div>
                </section>
            </div>

            <!-- ===================== RIGHT: price + seller ===================== -->
            <aside class="detail__side">
                <div class="panel">
                    <p class="price">{{ listing.price }}</p>
                    <div v-if="listing.isNegotiable" class="panel__tags">
                        <AppTag>Do negocjacji</AppTag>
                    </div>
                    <AppButton class="contact-btn" variant="primary" type="button">
                        Napisz do sprzedającego
                    </AppButton>
                    <p class="panel__note">
                        Skontaktuj się bezpośrednio ze sprzedającym. Bear nie pośredniczy w płatności.
                    </p>
                    <dl class="facts">
                        <div class="facts__row">
                            <dt>Lokalizacja</dt>
                            <dd>{{ listing.location }}</dd>
                        </div>
                        <div class="facts__row">
                            <dt>Dodano</dt>
                            <dd>{{ listing.postedAtFull }}</dd>
                        </div>
                        <div class="facts__row">
                            <dt>Nr ogłoszenia</dt>
                            <dd>{{ listing.code }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="panel seller">
                    <div class="seller__head">
                        <BearSeal :size="42" :ring="false" />
                        <div>
                            <SellerBadge :type="seller.type" />
                            <p class="seller__name">
                                <Link :href="`/sprzedawca/${seller.id}`" class="seller__link">{{ seller.name }}</Link>
                            </p>
                        </div>
                    </div>
                    <Link :href="`/sprzedawca/${seller.id}`" class="seller__all">Zobacz wszystkie ogłoszenia sprzedającego</Link>
                    <dl v-if="seller.company" class="facts">
                        <div class="facts__row">
                            <dt>Firma</dt>
                            <dd>{{ seller.company.name }}</dd>
                        </div>
                        <div v-if="seller.company.city" class="facts__row">
                            <dt>Adres</dt>
                            <dd>{{ seller.company.city }}</dd>
                        </div>
                        <div v-if="seller.company.taxId" class="facts__row">
                            <dt>NIP</dt>
                            <dd>{{ seller.company.taxId }}</dd>
                        </div>
                    </dl>
                    <p v-if="seller.memberSince" class="seller__since">Na Bear od {{ seller.memberSince }}</p>
                </div>
            </aside>
        </div>

        <!-- ===================== RELATED ===================== -->
        <SectionBlock
            v-if="related.length"
            class="related"
            eyebrow="Z tej samej kategorii"
            title="Zobacz też"
        >
            <div class="related__grid">
                <ListingCard
                    v-for="l in related"
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
        </SectionBlock>
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

/* Header */
.head {
    margin-bottom: clamp(1.4rem, 3vw, 2rem);
}
.head__title {
    font-family: var(--font-display);
    font-weight: 800;
    font-size: clamp(1.6rem, 4vw, 2.6rem);
    line-height: 1.02;
    letter-spacing: -0.03em;
    margin: 0;
    text-wrap: balance;
}
.head__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
    font-family: var(--font-mono);
    font-size: 0.8rem;
    color: var(--text-soft);
    margin: 0.9rem 0 0;
}
.head__meta .dot {
    opacity: 0.5;
}
.head__code {
    letter-spacing: 0.06em;
}

/* Layout */
.detail {
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(0, 1fr);
    gap: clamp(1.4rem, 3vw, 2.4rem);
    align-items: start;
}
.detail__main {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 1.8rem;
}

/* Gallery */
.gallery {
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
}
.gallery__main {
    aspect-ratio: 4 / 3;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid var(--line);
    background: var(--surface-2);
}
.gallery__main img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.gallery__placeholder {
    width: 100%;
    height: 100%;
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, #21302a, var(--seller-firma));
    color: #fff;
    opacity: 0.9;
}
.gallery__thumbs {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.gallery__thumb {
    width: 74px;
    height: 60px;
    padding: 0;
    border: 2px solid transparent;
    border-radius: 5px;
    overflow: hidden;
    cursor: pointer;
    background: var(--surface-2);
}
.gallery__thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.gallery__thumb.is-active {
    border-color: var(--accent);
}

/* Description */
.section-title {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 1.3rem;
    letter-spacing: -0.02em;
    margin: 0 0 0.9rem;
}
.desc__body {
    white-space: pre-line;
    color: var(--text);
    line-height: 1.7;
    max-width: 68ch;
}

/* Sidebar panels */
.detail__side {
    position: sticky;
    top: 88px;
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
    min-width: 0;
}
.panel {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: 1.3rem;
    box-shadow: var(--shadow);
}
.price {
    font-family: var(--font-mono);
    font-weight: 700;
    font-size: clamp(1.6rem, 3.5vw, 2.1rem);
    color: var(--text);
    margin: 0;
    font-variant-numeric: tabular-nums;
}
.panel__tags {
    margin-top: 0.7rem;
}
.contact-btn {
    width: 100%;
    justify-content: center;
    margin-top: 1.1rem;
}
.panel__note {
    font-size: 0.82rem;
    color: var(--text-soft);
    margin: 0.8rem 0 0;
}

/* Facts list */
.facts {
    margin: 1.1rem 0 0;
    padding-top: 1.1rem;
    border-top: 1px solid var(--line);
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}
.facts__row {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    font-size: 0.88rem;
}
.facts__row dt {
    color: var(--text-soft);
}
.facts__row dd {
    margin: 0;
    text-align: right;
    font-weight: 500;
}

/* Seller */
.seller__head {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}
.seller__name {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 1.1rem;
    margin: 0.35rem 0 0;
}
.seller__link {
    color: inherit;
    text-decoration: none;
}
.seller__link:hover {
    color: var(--accent-text);
    text-decoration: underline;
}
.seller__all {
    display: inline-block;
    margin-top: 1rem;
    font-family: var(--font-mono);
    font-size: 0.76rem;
    letter-spacing: 0.03em;
    color: var(--accent-text);
    text-decoration: none;
}
.seller__all:hover {
    text-decoration: underline;
}
.seller__since {
    font-family: var(--font-mono);
    font-size: 0.72rem;
    color: var(--text-soft);
    margin: 1rem 0 0;
}

/* Related */
.related {
    margin-top: clamp(2.6rem, 6vw, 4rem);
}
.related__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1.5rem;
}

@media (max-width: 860px) {
    .detail {
        grid-template-columns: 1fr;
    }
    .detail__side {
        position: static;
    }
}
</style>
