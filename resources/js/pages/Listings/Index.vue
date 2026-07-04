<script setup>
import { reactive, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import SiteHeader from '../../components/SiteHeader.vue';
import SiteFooter from '../../components/SiteFooter.vue';
import SearchField from '../../components/SearchField.vue';
import AppButton from '../../components/AppButton.vue';
import ListingCard from '../../components/ListingCard.vue';
import Pagination from '../../components/Pagination.vue';
import BearSeal from '../../components/BearSeal.vue';

const props = defineProps({
    listings: { type: Object, required: true }, // Laravel paginator
    categories: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const form = reactive({
    q: props.filters.q ?? '',
    category: props.filters.category ?? '',
    seller: props.filters.seller ?? '',
    price_min: props.filters.price_min ?? '',
    price_max: props.filters.price_max ?? '',
    sort: props.filters.sort ?? 'newest',
});

const hasActiveFilters = computed(
    () =>
        !!(form.q || form.category || form.seller || form.price_min || form.price_max) ||
        form.sort !== 'newest',
);

function query() {
    const q = {};
    if (form.q) q.q = form.q;
    if (form.category) q.category = form.category;
    if (form.seller) q.seller = form.seller;
    if (form.price_min !== '' && form.price_min !== null) q.price_min = form.price_min;
    if (form.price_max !== '' && form.price_max !== null) q.price_max = form.price_max;
    if (form.sort && form.sort !== 'newest') q.sort = form.sort;
    return q;
}

function submit() {
    router.get('/ogloszenia', query(), { preserveState: true, preserveScroll: true, replace: true });
}

function setSeller(value) {
    form.seller = form.seller === value ? '' : value;
    submit();
}

function reset() {
    form.q = '';
    form.category = '';
    form.seller = '';
    form.price_min = '';
    form.price_max = '';
    form.sort = 'newest';
    router.get('/ogloszenia', {}, { preserveScroll: true });
}

const sellerOptions = [
    { value: '', label: 'Wszyscy' },
    { value: 'prywatna', label: 'Osoba prywatna' },
    { value: 'firma', label: 'Firma' },
];

function ogloszenia(n) {
    const ones = n % 10;
    const tens = n % 100;
    if (n === 1) return 'ogłoszenie';
    if (ones >= 2 && ones <= 4 && !(tens >= 12 && tens <= 14)) return 'ogłoszenia';
    return 'ogłoszeń';
}
</script>

<template>
    <Head title="Ogłoszenia" />

    <SiteHeader />

    <main class="wrap">
        <header class="page-head">
            <p class="page-head__eyebrow">Przeglądaj</p>
            <h1 class="page-head__title">Ogłoszenia</h1>
            <div class="page-head__search">
                <SearchField v-model="form.q" size="lg" @submit="submit" />
            </div>
        </header>

        <div class="layout">
            <!-- ===================== FILTRY ===================== -->
            <aside class="filters" aria-label="Filtry">
                <div class="filters__head">
                    <h2 class="filters__title">Filtry</h2>
                    <button v-if="hasActiveFilters" type="button" class="filters__reset" @click="reset">
                        Wyczyść
                    </button>
                </div>

                <div class="filter">
                    <label class="filter__label" for="f-category">Kategoria</label>
                    <select id="f-category" v-model="form.category" class="filter__select" @change="submit">
                        <option value="">Wszystkie kategorie</option>
                        <option v-for="c in categories" :key="c.slug" :value="c.slug">{{ c.name }}</option>
                    </select>
                </div>

                <div class="filter">
                    <span class="filter__label">Kto sprzedaje</span>
                    <div class="segmented" role="group" aria-label="Typ sprzedawcy">
                        <button
                            v-for="opt in sellerOptions"
                            :key="opt.value"
                            type="button"
                            class="segmented__btn"
                            :class="{ 'is-active': form.seller === opt.value }"
                            :aria-pressed="form.seller === opt.value"
                            @click="setSeller(opt.value)"
                        >
                            {{ opt.label }}
                        </button>
                    </div>
                </div>

                <div class="filter">
                    <span class="filter__label">Cena (zł)</span>
                    <div class="price">
                        <input
                            v-model="form.price_min"
                            type="number"
                            min="0"
                            inputmode="numeric"
                            placeholder="od"
                            aria-label="Cena od"
                            class="filter__input"
                            @keyup.enter="submit"
                        />
                        <span class="price__dash">–</span>
                        <input
                            v-model="form.price_max"
                            type="number"
                            min="0"
                            inputmode="numeric"
                            placeholder="do"
                            aria-label="Cena do"
                            class="filter__input"
                            @keyup.enter="submit"
                        />
                    </div>
                    <AppButton variant="secondary" @click="submit">Zastosuj cenę</AppButton>
                </div>
            </aside>

            <!-- ===================== WYNIKI ===================== -->
            <section class="results" aria-label="Wyniki">
                <div class="results__bar">
                    <p class="results__count">
                        <strong>{{ listings.total }}</strong> {{ ogloszenia(listings.total) }}
                    </p>
                    <label class="sort">
                        <span class="sort__label">Sortuj:</span>
                        <select v-model="form.sort" class="filter__select sort__select" @change="submit">
                            <option value="newest">Najnowsze</option>
                            <option value="price_asc">Cena: rosnąco</option>
                            <option value="price_desc">Cena: malejąco</option>
                        </select>
                    </label>
                </div>

                <div v-if="listings.data.length" class="grid">
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

                <div v-else class="empty">
                    <BearSeal :size="72" :ring="false" />
                    <h3 class="empty__title">Nic tu nie ma — na razie</h3>
                    <p class="empty__text">Żadne ogłoszenie nie pasuje do tych filtrów. Poluzuj kryteria albo zacznij od nowa.</p>
                    <AppButton variant="secondary" @click="reset">Wyczyść filtry</AppButton>
                </div>

                <div v-if="listings.data.length" class="results__foot">
                    <Pagination :links="listings.links" />
                </div>
            </section>
        </div>
    </main>

    <SiteFooter />
</template>

<style scoped>
.wrap {
    max-width: 1180px;
    margin: 0 auto;
    padding: clamp(1.6rem, 4vw, 3rem) clamp(1rem, 4vw, 2.4rem);
}

/* Page header */
.page-head {
    padding-bottom: clamp(1.6rem, 4vw, 2.4rem);
    border-bottom: 1px solid var(--line);
    margin-bottom: clamp(1.6rem, 4vw, 2.4rem);
}
.page-head__eyebrow {
    font-family: var(--font-mono);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--text-soft);
    margin: 0 0 0.6rem;
}
.page-head__title {
    font-family: var(--font-display);
    font-weight: 800;
    font-size: clamp(2rem, 5vw, 3rem);
    letter-spacing: -0.035em;
    line-height: 0.95;
    margin: 0 0 1.4rem;
}
.page-head__search {
    max-width: 560px;
}

/* Layout */
.layout {
    display: grid;
    grid-template-columns: minmax(0, 250px) minmax(0, 1fr);
    gap: clamp(1.4rem, 3vw, 2.6rem);
    align-items: start;
}

/* Filters */
.filters {
    position: sticky;
    top: 88px;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: 1.3rem;
}
.filters__head {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    border-bottom: 1px solid var(--line);
    padding-bottom: 0.9rem;
}
.filters__title {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 1.15rem;
    margin: 0;
}
.filters__reset {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    background: none;
    border: 0;
    color: var(--accent-text);
    cursor: pointer;
    padding: 0;
}
.filters__reset:hover {
    text-decoration: underline;
}
.filter {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}
.filter__label {
    font-family: var(--font-mono);
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--text-soft);
}
.filter__select,
.filter__input {
    font-family: var(--font-sans);
    font-size: 0.95rem;
    color: var(--text);
    background: var(--bg);
    border: 1.5px solid var(--line-strong);
    border-radius: 4px;
    padding: 0.6rem 0.7rem;
    width: 100%;
}
.filter__select:focus,
.filter__input:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 28%, transparent);
}

/* Segmented seller control */
.segmented {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}
.segmented__btn {
    font-family: var(--font-sans);
    font-size: 0.9rem;
    text-align: left;
    background: var(--bg);
    border: 1.5px solid var(--line-strong);
    border-radius: 4px;
    padding: 0.5rem 0.7rem;
    color: var(--text);
    cursor: pointer;
    transition: border-color 0.15s ease;
}
.segmented__btn:hover {
    border-color: var(--text);
}
.segmented__btn.is-active {
    border-color: var(--accent);
    background: color-mix(in srgb, var(--accent) 14%, transparent);
    font-weight: 600;
}

/* Price row */
.price {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.price__dash {
    color: var(--text-soft);
}

/* Results */
.results {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 1.6rem;
}
.results__bar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.8rem;
    align-items: center;
    justify-content: space-between;
}
.results__count {
    margin: 0;
    color: var(--text-soft);
    font-size: 0.95rem;
}
.results__count strong {
    color: var(--text);
    font-family: var(--font-mono);
    font-variant-numeric: tabular-nums;
}
.sort {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}
.sort__label {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-soft);
    white-space: nowrap;
}
.sort__select {
    width: auto;
}
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1.5rem;
}
.results__foot {
    margin-top: 0.6rem;
}

/* Empty state */
.empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 0.7rem;
    padding: clamp(2.5rem, 8vw, 5rem) 1rem;
    border: 1px dashed var(--line-strong);
    border-radius: 8px;
}
.empty :deep(svg) {
    color: var(--text-soft);
    opacity: 0.6;
}
.empty__title {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 1.3rem;
    margin: 0.4rem 0 0;
}
.empty__text {
    color: var(--text-soft);
    max-width: 40ch;
    margin: 0 0 0.6rem;
}

@media (max-width: 820px) {
    .layout {
        grid-template-columns: 1fr;
    }
    .filters {
        position: static;
    }
}
</style>
