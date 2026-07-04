<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import BearSeal from '../components/BearSeal.vue';
import SectionBlock from '../components/SectionBlock.vue';
import AppButton from '../components/AppButton.vue';
import SellerBadge from '../components/SellerBadge.vue';
import AppTag from '../components/AppTag.vue';
import SearchField from '../components/SearchField.vue';
import ListingCard from '../components/ListingCard.vue';

const theme = ref('auto');
function setTheme(next) {
    theme.value = next;
    const root = document.documentElement;
    if (next === 'auto') {
        delete root.dataset.theme;
    } else {
        root.dataset.theme = next;
    }
}

const query = ref('');

const palette = [
    { name: 'Sosnowy tusz', brand: '--color-pine', semantic: '--text / --seal', hex: '#21302A', role: 'Tekst i pieczęć', swatch: '#21302A' },
    { name: 'Papier brzozowy', brand: '--color-paper', semantic: '--bg', hex: '#EAE1CE', role: 'Tło', swatch: '#EAE1CE' },
    { name: 'Miód', brand: '--color-honey', semantic: '--accent', hex: '#C9821F', role: 'Akcent i akcja', swatch: '#C9821F' },
    { name: 'Mech', brand: '--color-moss', semantic: '--seller-priv', hex: '#566B4D', role: 'Osoba prywatna', swatch: '#566B4D' },
    { name: 'Żurawina', brand: '--color-cranberry', semantic: '--seller-firma', hex: '#8A3140', role: 'Firma', swatch: '#8A3140' },
    { name: 'Taupe łąkowy', brand: '--color-taupe', semantic: '--text-soft', hex: '#5F5648', role: 'Tekst drugorzędny', swatch: '#5F5648' },
];

const scale = [
    { token: 'wordmark', size: 'clamp(3.6rem, 15vw, 8.2rem)', role: 'Znak / hasło', face: 'Bricolage 800' },
    { token: 'display-lg', size: 'clamp(1.5rem, 3.4vw, 2.1rem)', role: 'Nagłówek sekcji', face: 'Bricolage 600' },
    { token: 'title', size: '1.12rem', role: 'Tytuł ogłoszenia', face: 'Bricolage 600' },
    { token: 'body', size: '1rem – 1.06rem', role: 'Tekst', face: 'Instrument 400' },
    { token: 'label', size: '0.72rem', role: 'Etykiety, dane', face: 'Space Mono 700' },
];

// Rendered verbatim in the usage panels — kept as strings so Vue doesn't parse them.
const usage = {
    button: '<AppButton variant="primary">Wystaw ogłoszenie</AppButton>\n<AppButton variant="secondary">Przeglądaj</AppButton>\n<AppButton variant="ghost" href="/obserwowane">Obserwowane →</AppButton>',
    badge: '<SellerBadge type="firma" />\n<SellerBadge type="prywatna" />',
    tag: '<AppTag variant="honey">Wyróżnione</AppTag>\n<AppTag>Do negocjacji</AppTag>',
    search: '<SearchField v-model="query" @submit="szukaj" />',
    seal: '<BearSeal :size="120" />          <!-- pełna pieczęć -->\n<BearSeal :size="46" :ring="false" /> <!-- znak zaufania -->',
    card: '<ListingCard\n  category="Rowery / Miejskie"\n  title="Rower miejski, damka"\n  price="450 zł"\n  location="Wrocław, Krzyki"\n  posted-at="wczoraj, 18:20"\n  seller-name="Marta K."\n  seller-type="prywatna"\n  code="OGL-8790"\n/>',
};
</script>

<template>
    <Head title="Design Cheatsheet" />

    <div class="page">
        <div class="wrap">
            <!-- Masthead -->
            <header class="masthead">
                <BearSeal :size="88" />
                <div class="masthead__text">
                    <p class="kicker">Bear Marketplace · dokumentacja dla developmentu</p>
                    <h1 class="title">Design Cheatsheet</h1>
                    <p class="lede">
                        Jedno źródło prawdy dla interfejsu: kolory, pismo i gotowe komponenty
                        z <code>resources/js/components</code>. Buduj widoki produkcyjne z tych
                        elementów, zamiast odtwarzać style od zera.
                    </p>
                </div>
                <div class="theme" role="group" aria-label="Motyw podglądu">
                    <button
                        v-for="opt in ['auto', 'light', 'dark']"
                        :key="opt"
                        type="button"
                        class="theme__btn"
                        :class="{ 'is-active': theme === opt }"
                        :aria-pressed="theme === opt"
                        @click="setTheme(opt)"
                    >
                        {{ opt === 'auto' ? 'Auto' : opt === 'light' ? 'Jasny' : 'Ciemny' }}
                    </button>
                </div>
            </header>

            <div class="sheet">
                <!-- PALETTE -->
                <SectionBlock
                    eyebrow="Paleta"
                    title="Las, miód i papier"
                    note="Kolory marki są stałe; tokeny semantyczne (--bg, --text, --accent…) przełączają się między motywami. W komponentach używaj tokenów, nie surowego hexa."
                >
                    <div class="chips">
                        <div v-for="c in palette" :key="c.name" class="chip">
                            <div class="chip__swatch" :style="{ background: c.swatch }"></div>
                            <div class="chip__name">{{ c.name }}</div>
                            <div class="chip__role">{{ c.role }}</div>
                            <div class="chip__tokens">
                                <code>{{ c.brand }}</code>
                                <code class="chip__semantic">{{ c.semantic }}</code>
                            </div>
                            <div class="chip__hex">{{ c.hex }}</div>
                        </div>
                    </div>
                </SectionBlock>

                <!-- TYPE -->
                <SectionBlock
                    eyebrow="Typografia"
                    title="Krój z charakterem, dane pod kontrolą"
                    note="Trzy role. Krój do danych trzyma ceny, kody ogłoszeń i kategorie równo w kolumnach — dlatego jest monospaced, nie dla ozdoby."
                >
                    <div class="specimens">
                        <div class="spec">
                            <div class="spec__aa spec__aa--display">Aa</div>
                            <div>
                                <p class="spec__role">Nagłówek — <code>font-display</code></p>
                                <p class="spec__name">Bricolage Grotesque · 600 / 800</p>
                                <p class="spec__sample spec__sample--display">Sprzedaj. Kup. Bez ceregieli.</p>
                                <p class="spec__glyphs">Bb Cc Ćć Ęę Łł Śś Żż Óó Ąą Ńń</p>
                            </div>
                        </div>
                        <div class="spec">
                            <div class="spec__aa spec__aa--body">Aa</div>
                            <div>
                                <p class="spec__role">Tekst — <code>font-sans</code></p>
                                <p class="spec__name">Instrument Sans · 400 / 600</p>
                                <p class="spec__sample">
                                    Opis ogłoszenia i szczegóły czyta się tu wygodnie w dłuższych akapitach.
                                    Miara wiersza trzyma się okolic sześćdziesięciu pięciu znaków.
                                </p>
                            </div>
                        </div>
                        <div class="spec">
                            <div class="spec__aa spec__aa--mono">Aa</div>
                            <div>
                                <p class="spec__role">Dane — <code>font-mono</code></p>
                                <p class="spec__name">Space Mono · 400 / 700</p>
                                <div class="dataline">
                                    <span class="dataline__price">1 190 zł</span>
                                    <span>OGL-8842</span>
                                    <span>SPORT / NARTY</span>
                                    <span>Kraków</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="scale">
                        <div v-for="s in scale" :key="s.token" class="scale__row">
                            <code class="scale__token">{{ s.token }}</code>
                            <span class="scale__size">{{ s.size }}</span>
                            <span class="scale__role">{{ s.role }}</span>
                            <span class="scale__face">{{ s.face }}</span>
                        </div>
                    </div>
                </SectionBlock>

                <!-- COMPONENTS -->
                <SectionBlock
                    eyebrow="Komponenty"
                    title="Słownik interfejsu"
                    note="Gotowe do importu. Każdy przycisk mówi, co robi; kolor odznaki od razu zdradza, kto sprzedaje."
                >
                    <div class="specimen-cards">
                        <div class="demo">
                            <div class="demo__stage">
                                <AppButton variant="primary">Wystaw ogłoszenie</AppButton>
                                <AppButton variant="secondary">Przeglądaj</AppButton>
                                <AppButton variant="ghost">Obserwowane →</AppButton>
                            </div>
                            <pre class="demo__code"><code>{{ usage.button }}</code></pre>
                        </div>

                        <div class="demo">
                            <div class="demo__stage">
                                <SellerBadge type="prywatna" />
                                <SellerBadge type="firma" />
                            </div>
                            <pre class="demo__code"><code>{{ usage.badge }}</code></pre>
                        </div>

                        <div class="demo">
                            <div class="demo__stage">
                                <AppTag variant="honey">Wyróżnione</AppTag>
                                <AppTag>Do negocjacji</AppTag>
                                <AppTag>Nowe</AppTag>
                                <AppTag>Z dowozem</AppTag>
                            </div>
                            <pre class="demo__code"><code>{{ usage.tag }}</code></pre>
                        </div>

                        <div class="demo">
                            <div class="demo__stage demo__stage--block">
                                <SearchField v-model="query" />
                                <p class="demo__hint">Wpisane: <code>{{ query || '—' }}</code></p>
                            </div>
                            <pre class="demo__code"><code>{{ usage.search }}</code></pre>
                        </div>

                        <div class="demo">
                            <div class="demo__stage">
                                <BearSeal :size="120" />
                                <BearSeal :size="46" :ring="false" />
                            </div>
                            <pre class="demo__code"><code>{{ usage.seal }}</code></pre>
                        </div>
                    </div>
                </SectionBlock>

                <!-- LISTING CARD -->
                <SectionBlock
                    eyebrow="Karta ogłoszenia"
                    title="System w jednym kafelku"
                    note="Złożenie wszystkich elementów. Miniaturę przekazujesz przez slot #thumb; domyślnie jest brandowany placeholder."
                >
                    <div class="cards">
                        <ListingCard
                            category="Sport / Narty"
                            title="Narty Rossignol Experience 82, 168 cm"
                            price="1 190 zł"
                            location="Kraków, Podgórze"
                            posted-at="dziś, 9:41"
                            seller-name="Alpin Sport"
                            seller-type="firma"
                            code="OGL-8842"
                            thumb-background="linear-gradient(135deg, #2b3d33, #8a3140)"
                        >
                            <template #thumb>
                                <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M8 44 L26 14 L38 14" />
                                    <path d="M20 50 L44 20" />
                                    <path d="M32 54 L52 26" />
                                    <path d="M14 47 L38 17" />
                                </svg>
                            </template>
                        </ListingCard>

                        <ListingCard
                            category="Rowery / Miejskie"
                            title="Rower miejski, damka — stan bardzo dobry"
                            price="450 zł"
                            location="Wrocław, Krzyki"
                            posted-at="wczoraj, 18:20"
                            seller-name="Marta K."
                            seller-type="prywatna"
                            code="OGL-8790"
                            thumb-background="linear-gradient(135deg, #33422f, #c9821f)"
                        >
                            <template #thumb>
                                <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="17" cy="43" r="10" />
                                    <circle cx="47" cy="43" r="10" />
                                    <path d="M17 43 L30 22 L44 22" />
                                    <path d="M47 43 L36 22" />
                                    <path d="M24 22 L38 22" />
                                    <path d="M30 22 L26 43" />
                                </svg>
                            </template>
                        </ListingCard>
                    </div>
                    <pre class="demo__code demo__code--wide"><code>{{ usage.card }}</code></pre>
                </SectionBlock>

                <footer class="foot">
                    <span>Bear Marketplace · Design Cheatsheet</span>
                    <span>resources/js/components</span>
                </footer>
            </div>
        </div>
    </div>
</template>

<style scoped>
.page {
    min-height: 100vh;
    background-image: radial-gradient(circle at 1px 1px, var(--line) 1px, transparent 0);
    background-size: 22px 22px;
}
.wrap {
    max-width: 1120px;
    margin: 0 auto;
    padding: clamp(1.4rem, 4vw, 3.4rem);
}
code {
    font-family: var(--font-mono);
    font-size: 0.85em;
}

/* Masthead */
.masthead {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: clamp(1.2rem, 3vw, 2.2rem);
    align-items: center;
    padding-bottom: clamp(2rem, 5vw, 3.2rem);
    border-bottom: 1px solid var(--line);
    margin-bottom: clamp(2.6rem, 6vw, 4.4rem);
}
.kicker {
    font-family: var(--font-mono);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--accent);
    margin: 0 0 0.6rem;
}
.title {
    font-family: var(--font-display);
    font-weight: 800;
    font-size: clamp(2.1rem, 6vw, 3.4rem);
    letter-spacing: -0.035em;
    line-height: 0.95;
    margin: 0;
    text-wrap: balance;
}
.lede {
    color: var(--text-soft);
    max-width: 60ch;
    margin: 0.9rem 0 0;
}
.theme {
    display: inline-flex;
    border: 1px solid var(--line-strong);
    border-radius: 999px;
    padding: 3px;
    background: var(--surface);
}
.theme__btn {
    font-family: var(--font-mono);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    border: 0;
    background: transparent;
    color: var(--text-soft);
    padding: 0.4rem 0.8rem;
    border-radius: 999px;
    cursor: pointer;
}
.theme__btn.is-active {
    background: var(--accent);
    color: var(--accent-ink);
}

.sheet {
    display: flex;
    flex-direction: column;
    gap: clamp(3.4rem, 7vw, 5.6rem);
}

/* Palette */
.chips {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 1px;
    background: var(--line);
    border: 1px solid var(--line);
    border-radius: 4px;
    overflow: hidden;
}
.chip {
    background: var(--surface);
    padding: 1.1rem 1.15rem;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    min-height: 190px;
}
.chip__swatch {
    height: 46px;
    border-radius: 3px;
    border: 1px solid var(--line-strong);
}
.chip__name {
    font-weight: 600;
    font-size: 0.98rem;
}
.chip__role {
    font-family: var(--font-mono);
    font-size: 0.64rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--text-soft);
}
.chip__tokens {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    margin-top: auto;
}
.chip__tokens code {
    font-size: 0.72rem;
    color: var(--text);
}
.chip__semantic {
    color: var(--text-soft) !important;
}
.chip__hex {
    font-family: var(--font-mono);
    font-size: 0.74rem;
    color: var(--text-soft);
    letter-spacing: 0.04em;
}

/* Type specimens */
.specimens {
    display: flex;
    flex-direction: column;
    gap: 1px;
    background: var(--line);
    border: 1px solid var(--line);
    border-radius: 4px;
    overflow: hidden;
}
.spec {
    background: var(--surface);
    padding: clamp(1.3rem, 3vw, 2rem);
    display: grid;
    grid-template-columns: minmax(0, 130px) 1fr;
    gap: clamp(1rem, 3vw, 2.2rem);
    align-items: center;
}
.spec__aa {
    font-size: clamp(3rem, 8vw, 4.6rem);
    line-height: 1;
    letter-spacing: -0.03em;
}
.spec__aa--display {
    font-family: var(--font-display);
    font-weight: 800;
}
.spec__aa--body {
    font-family: var(--font-sans);
    font-weight: 600;
}
.spec__aa--mono {
    font-family: var(--font-mono);
    font-weight: 700;
}
.spec__role {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--accent);
    margin: 0 0 0.4rem;
}
.spec__role code {
    color: var(--accent);
}
.spec__name {
    font-weight: 600;
    font-size: 1.02rem;
    margin: 0 0 0.6rem;
}
.spec__sample {
    margin: 0;
    max-width: 52ch;
}
.spec__sample--display {
    font-family: var(--font-display);
    font-weight: 800;
    font-size: clamp(1.4rem, 3.6vw, 2.1rem);
    letter-spacing: -0.03em;
    line-height: 1.02;
}
.spec__glyphs {
    font-family: var(--font-mono);
    font-size: 0.72rem;
    color: var(--text-soft);
    letter-spacing: 0.06em;
    margin: 0.6rem 0 0;
}
.dataline {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem 0.9rem;
    font-family: var(--font-mono);
    font-size: 0.95rem;
    font-variant-numeric: tabular-nums;
}
.dataline span {
    border: 1px solid var(--line-strong);
    border-radius: 3px;
    padding: 0.28rem 0.6rem;
    letter-spacing: 0.04em;
}
.dataline__price {
    background: var(--accent);
    color: var(--accent-ink);
    border-color: transparent !important;
    font-weight: 700;
}

/* Type scale table */
.scale {
    display: flex;
    flex-direction: column;
    border: 1px solid var(--line);
    border-radius: 4px;
    overflow: hidden;
}
.scale__row {
    display: grid;
    grid-template-columns: 130px 1fr 1fr auto;
    gap: 1rem;
    align-items: baseline;
    padding: 0.75rem 1.1rem;
    background: var(--surface);
    border-top: 1px solid var(--line);
    font-size: 0.85rem;
}
.scale__row:first-child {
    border-top: 0;
}
.scale__token {
    font-weight: 700;
    color: var(--text);
}
.scale__size {
    font-family: var(--font-mono);
    color: var(--text-soft);
    font-size: 0.78rem;
}
.scale__role {
    color: var(--text);
}
.scale__face {
    font-family: var(--font-mono);
    font-size: 0.72rem;
    color: var(--text-soft);
    text-align: right;
}

/* Component demos */
.specimen-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.4rem;
}
.demo {
    display: flex;
    flex-direction: column;
    border: 1px solid var(--line);
    border-radius: 6px;
    overflow: hidden;
    background: var(--surface);
}
.demo__stage {
    padding: 1.5rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.7rem;
    align-items: center;
    flex: 1;
}
.demo__stage--block {
    flex-direction: column;
    align-items: stretch;
}
.demo__hint {
    font-family: var(--font-mono);
    font-size: 0.74rem;
    color: var(--text-soft);
    margin: 0;
}
.demo__code {
    margin: 0;
    padding: 1rem 1.15rem;
    background: var(--surface-2);
    border-top: 1px solid var(--line);
    overflow-x: auto;
    font-size: 0.78rem;
    line-height: 1.5;
    color: var(--text);
}
.demo__code--wide {
    border: 1px solid var(--line);
    border-radius: 6px;
    margin-top: 1.5rem;
}

/* Cards */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.6rem;
}

.foot {
    border-top: 1px solid var(--line);
    padding-top: 1.5rem;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 0.6rem;
    font-family: var(--font-mono);
    font-size: 0.72rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-soft);
}

@media (max-width: 780px) {
    .masthead {
        grid-template-columns: 1fr;
    }
    .spec {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    .scale__row {
        grid-template-columns: 1fr 1fr;
    }
    .scale__face {
        text-align: left;
    }
}
</style>
