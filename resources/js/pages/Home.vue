<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import SiteHeader from '../components/SiteHeader.vue';
import SiteFooter from '../components/SiteFooter.vue';
import SearchField from '../components/SearchField.vue';
import AppButton from '../components/AppButton.vue';
import AppTag from '../components/AppTag.vue';
import BearSeal from '../components/BearSeal.vue';
import SectionBlock from '../components/SectionBlock.vue';
import CategoryTile from '../components/CategoryTile.vue';
import CategoryGlyph from '../components/CategoryGlyph.vue';
import ListingCard from '../components/ListingCard.vue';

/**
 * Props default to mock data so the page renders standalone; a controller can
 * later pass real categories/listings/stats without any template changes.
 */
defineProps({
    categories: {
        type: Array,
        default: () => [
            { name: 'Motoryzacja', icon: 'motoryzacja', count: '3 120' },
            { name: 'Rowery', icon: 'rowery', count: '1 480' },
            { name: 'Elektronika', icon: 'elektronika', count: '2 640' },
            { name: 'Dom i ogród', icon: 'dom', count: '1 950' },
            { name: 'Moda', icon: 'moda', count: '2 210' },
            { name: 'Sport i hobby', icon: 'sport', count: '980' },
            { name: 'Dla dzieci', icon: 'dzieci', count: '1 130' },
            { name: 'Zwierzęta', icon: 'zwierzeta', count: '420' },
        ],
    },
    listings: {
        type: Array,
        default: () => [
            { category: 'Dom i ogród', title: 'Kosiarka spalinowa Stihl, model 2024', price: '1 890 zł', location: 'Poznań, Grunwald', postedAt: 'dziś, 8:05', sellerName: 'Zielony Ogród Sp. z o.o.', sellerType: 'firma', code: 'OGL-9142', icon: 'dom', accent: '#566b4d' },
            { category: 'Rowery / Górskie', title: 'Rower górski Kross Level 5.0, rozm. L', price: '2 300 zł', location: 'Katowice, Ligota', postedAt: 'dziś, 10:22', sellerName: 'Tomasz W.', sellerType: 'prywatna', code: 'OGL-9138', icon: 'rowery', accent: '#c9821f' },
            { category: 'Elektronika / Telefony', title: 'iPhone 13 128 GB, bez rys, komplet', price: '1 650 zł', location: 'Warszawa, Mokotów', postedAt: 'wczoraj, 19:40', sellerName: 'Anna P.', sellerType: 'prywatna', code: 'OGL-9101', icon: 'elektronika', accent: '#8a3140' },
            { category: 'Dom i ogród / Meble', title: 'Fotel biurowy ergonomiczny, nowy', price: '640 zł', location: 'Łódź, Śródmieście', postedAt: 'wczoraj, 14:10', sellerName: 'Meble Kowalski', sellerType: 'firma', code: 'OGL-9077', icon: 'dom', accent: '#c9821f' },
            { category: 'Dla dzieci / Wózki', title: 'Wózek 3w1 Riko, kompletny', price: '800 zł', location: 'Gdańsk, Wrzeszcz', postedAt: 'wczoraj, 12:33', sellerName: 'Karolina M.', sellerType: 'prywatna', code: 'OGL-9064', icon: 'dzieci', accent: '#566b4d' },
            { category: 'Motoryzacja / Opony', title: 'Opony zimowe 205/55 R16, 4 szt.', price: '720 zł', location: 'Kraków, Nowa Huta', postedAt: 'przedwczoraj', sellerName: 'AutoSerwis Nowak', sellerType: 'firma', code: 'OGL-9048', icon: 'motoryzacja', accent: '#8a3140' },
        ],
    },
    stats: {
        type: Object,
        default: () => ({ listings: '12 480', cities: '320' }),
    },
});

const steps = [
    { title: 'Znajdź albo wystaw', text: 'Przeglądaj ogłoszenia w swojej okolicy albo dodaj własne w dwie minuty.' },
    { title: 'Dogadajcie się', text: 'Napisz lub zadzwoń do ogłoszeniodawcy i ustalcie szczegóły bezpośrednio.' },
    { title: 'Sfinalizujcie', text: 'Spotkajcie się, obejrzyjcie, sfinalizujcie. Bear nie bierze prowizji.' },
];

const query = ref('');
const popular = ['Rowery', 'Elektronika', 'Dom i ogród', 'Motoryzacja'];

function thumbBackground(accent) {
    return `linear-gradient(135deg, #21302a, ${accent})`;
}
</script>

<template>
    <Head title="Kupuj i sprzedawaj bez ceregieli" />

    <SiteHeader />

    <main>
        <!-- ===================== HERO (two tracks) ===================== -->
        <section class="hero">
            <div class="hero__inner">
                <div class="hero__buy">
                    <p class="hero__eyebrow">Ogłoszenia w całej Polsce</p>
                    <h1 class="hero__title">Kupuj i sprzedawaj bez ceregieli.</h1>
                    <p class="hero__lede">
                        Ogłoszenia od osób prywatnych i firm spotykają się tu w jednym,
                        spokojnym miejscu. Zacznij od wyszukania — albo wystaw własne.
                    </p>
                    <SearchField v-model="query" size="lg" @submit="() => {}" />
                    <div class="hero__popular">
                        <span class="hero__popular-label">Popularne:</span>
                        <a v-for="p in popular" :key="p" href="#kategorie" class="hero__chip">{{ p }}</a>
                    </div>
                </div>

                <aside class="sell" aria-label="Wystaw ogłoszenie">
                    <BearSeal :size="52" :ring="false" class="sell__seal" />
                    <p class="sell__eyebrow">Sprzedajesz?</p>
                    <h2 class="sell__title">Wystaw ogłoszenie w dwie minuty.</h2>
                    <p class="sell__text">Za darmo, bez prowizji. Osoba prywatna czy firma — każdy jest u siebie.</p>
                    <AppButton href="/wystaw" variant="primary">Wystaw ogłoszenie</AppButton>
                    <p class="sell__stat">
                        <strong>{{ stats.listings }}</strong> aktywnych ogłoszeń ·
                        <strong>{{ stats.cities }}</strong> miast
                    </p>
                </aside>
            </div>
        </section>

        <div class="wrap sections">
            <!-- ===================== KATEGORIE ===================== -->
            <SectionBlock
                id="kategorie"
                eyebrow="Kategorie"
                title="Zacznij od tego, czego szukasz"
                note="Osiem głównych działów. Liczba przy każdym pokazuje, ile ogłoszeń czeka teraz."
            >
                <div class="cat-grid">
                    <CategoryTile
                        v-for="c in categories"
                        :key="c.name"
                        :name="c.name"
                        :icon="c.icon"
                        :count="c.count"
                        href="#swieze"
                    />
                </div>
            </SectionBlock>

            <!-- ===================== ŚWIEŻE OGŁOSZENIA ===================== -->
            <SectionBlock
                id="swieze"
                eyebrow="Świeże ogłoszenia"
                title="Dopiero co dodane"
                note="Najnowsze oferty od osób prywatnych i firm. Kolor odznaki mówi, kto sprzedaje."
            >
                <template #default>
                    <div class="listings">
                        <ListingCard
                            v-for="l in listings"
                            :key="l.code"
                            :category="l.category"
                            :title="l.title"
                            :price="l.price"
                            :location="l.location"
                            :posted-at="l.postedAt"
                            :seller-name="l.sellerName"
                            :seller-type="l.sellerType"
                            :code="l.code"
                            :thumb-background="thumbBackground(l.accent)"
                        >
                            <template #thumb>
                                <CategoryGlyph :name="l.icon" />
                            </template>
                        </ListingCard>
                    </div>
                    <div class="listings__more">
                        <AppButton href="#swieze" variant="secondary">Zobacz wszystkie ogłoszenia</AppButton>
                    </div>
                </template>
            </SectionBlock>

            <!-- ===================== JAK TO DZIAŁA ===================== -->
            <SectionBlock
                id="jak-to-dziala"
                eyebrow="Jak to działa"
                title="Trzy kroki, zero prowizji"
                note="Bear łączy kupujących ze sprzedającymi i schodzi z drogi. Reszta należy do was."
            >
                <ol class="steps">
                    <li v-for="(s, i) in steps" :key="s.title" class="step">
                        <span class="step__num">{{ String(i + 1).padStart(2, '0') }}</span>
                        <h3 class="step__title">{{ s.title }}</h3>
                        <p class="step__text">{{ s.text }}</p>
                    </li>
                </ol>
            </SectionBlock>

            <!-- ===================== CLOSING CTA ===================== -->
            <section class="cta">
                <div class="cta__text">
                    <h2 class="cta__title">Masz coś, co komuś się przyda?</h2>
                    <p class="cta__note">Twoje ogłoszenie zobaczą kupujący z całej Polski. Wystawienie jest darmowe.</p>
                </div>
                <AppButton href="/wystaw" variant="primary">Wystaw ogłoszenie</AppButton>
            </section>
        </div>
    </main>

    <SiteFooter />
</template>

<style scoped>
.wrap {
    max-width: 1180px;
    margin: 0 auto;
    padding-left: clamp(1rem, 4vw, 2.4rem);
    padding-right: clamp(1rem, 4vw, 2.4rem);
}

/* ---------------- Hero ---------------- */
.hero {
    background-image: radial-gradient(circle at 1px 1px, var(--line) 1px, transparent 0);
    background-size: 22px 22px;
    border-bottom: 1px solid var(--line);
}
.hero__inner {
    max-width: 1180px;
    margin: 0 auto;
    padding: clamp(2.6rem, 7vw, 5.5rem) clamp(1rem, 4vw, 2.4rem);
    display: grid;
    grid-template-columns: minmax(0, 1.55fr) minmax(0, 1fr);
    gap: clamp(1.6rem, 4vw, 3.4rem);
    align-items: center;
}
.hero__eyebrow {
    font-family: var(--font-mono);
    font-size: 0.74rem;
    font-weight: 700;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--accent-text);
    margin: 0 0 1rem;
}
.hero__title {
    font-family: var(--font-display);
    font-weight: 800;
    font-size: clamp(2.5rem, 6vw, 4.2rem);
    line-height: 0.98;
    letter-spacing: -0.04em;
    margin: 0;
    text-wrap: balance;
    max-width: 15ch;
}
.hero__lede {
    color: var(--text-soft);
    font-size: 1.08rem;
    max-width: 46ch;
    margin: 1.2rem 0 1.8rem;
}
.hero__popular {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem 0.8rem;
    margin-top: 1.1rem;
}
.hero__popular-label {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--text-soft);
}
.hero__chip {
    font-size: 0.88rem;
    text-decoration: none;
    color: var(--text);
    border: 1px solid var(--line-strong);
    border-radius: 999px;
    padding: 0.3rem 0.8rem;
    transition: border-color 0.15s ease, color 0.15s ease;
}
.hero__chip:hover {
    border-color: var(--accent);
    color: var(--accent-text);
}

/* Seller panel */
.sell {
    background: var(--surface);
    border: 1px solid var(--line-strong);
    border-radius: 8px;
    padding: clamp(1.5rem, 3vw, 2rem);
    box-shadow: var(--shadow);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
}
.sell__seal {
    margin-bottom: 0.4rem;
}
.sell__eyebrow {
    font-family: var(--font-mono);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--seller-firma);
    margin: 0;
}
.sell__title {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 1.4rem;
    line-height: 1.08;
    letter-spacing: -0.02em;
    margin: 0.2rem 0 0;
    text-wrap: balance;
}
.sell__text {
    color: var(--text-soft);
    font-size: 0.95rem;
    margin: 0.3rem 0 1rem;
}
.sell__stat {
    font-family: var(--font-mono);
    font-size: 0.76rem;
    color: var(--text-soft);
    letter-spacing: 0.02em;
    margin: 1.1rem 0 0;
    padding-top: 1rem;
    border-top: 1px solid var(--line);
    width: 100%;
    font-variant-numeric: tabular-nums;
}
.sell__stat strong {
    color: var(--text);
    font-weight: 700;
}

/* ---------------- Sections rhythm ---------------- */
.sections {
    display: flex;
    flex-direction: column;
    gap: clamp(3.4rem, 7vw, 5.6rem);
    padding-top: clamp(3rem, 6vw, 5rem);
}

.cat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1rem;
}

.listings {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
    gap: 1.6rem;
}
.listings__more {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}

/* ---------------- How it works ---------------- */
.steps {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1px;
    background: var(--line);
    border: 1px solid var(--line);
    border-radius: 6px;
    overflow: hidden;
    counter-reset: none;
}
.step {
    background: var(--surface);
    padding: clamp(1.4rem, 3vw, 2rem);
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.step__num {
    font-family: var(--font-mono);
    font-weight: 700;
    font-size: 1.4rem;
    color: var(--accent-text);
    letter-spacing: 0.02em;
}
.step__title {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 1.2rem;
    letter-spacing: -0.015em;
    margin: 0.2rem 0 0;
}
.step__text {
    color: var(--text-soft);
    margin: 0;
    font-size: 0.95rem;
}

/* ---------------- Closing CTA ---------------- */
.cta {
    background: var(--surface-2);
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: clamp(1.8rem, 4vw, 2.8rem);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1.2rem;
}
.cta__title {
    font-family: var(--font-display);
    font-weight: 700;
    font-size: clamp(1.5rem, 3.4vw, 2.1rem);
    letter-spacing: -0.02em;
    margin: 0;
    text-wrap: balance;
}
.cta__note {
    color: var(--text-soft);
    margin: 0.5rem 0 0;
    max-width: 52ch;
}

/* ---------------- Responsive ---------------- */
@media (max-width: 880px) {
    .hero__inner {
        grid-template-columns: 1fr;
    }
    .steps {
        grid-template-columns: 1fr;
    }
}
</style>
