<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import PanelLayout from '../../components/PanelLayout.vue';
import AppButton from '../../components/AppButton.vue';
import { STATUS_META, STATUS_ORDER } from '../../lib/listingStatus';

const props = defineProps({
    user: { type: Object, required: true },
    konto: { type: String, default: null },
    overview: { type: Object, required: true },
});

const greeting = computed(() => (props.user.type === 'firma' ? 'Dzień dobry' : 'Cześć'));
const listingsHref = computed(() => (props.konto ? `/panel/ogloszenia?konto=${props.konto}` : '/panel/ogloszenia'));

// Portfolio bar.
const segments = computed(() =>
    STATUS_ORDER.map((key) => ({ key, color: STATUS_META[key].color, count: props.overview[key] ?? 0 })).filter((s) => s.count > 0),
);
const total = computed(() => props.overview.total || 0);
function pct(count) {
    return total.value ? (count / total.value) * 100 : 0;
}
const grown = ref(false);
onMounted(() => requestAnimationFrame(() => (grown.value = true)));

// Activity sparkline.
const W = 260;
const H = 62;
const PAD = 6;
const activity = computed(() => props.overview.activity ?? []);
const activityTotal = computed(() => activity.value.reduce((sum, a) => sum + a.count, 0));
const pts = computed(() => {
    const a = activity.value;
    if (a.length < 2) return [];
    const max = Math.max(1, ...a.map((d) => d.count));
    const stepX = (W - 2 * PAD) / (a.length - 1);
    return a.map((d, i) => [PAD + i * stepX, H - PAD - (d.count / max) * (H - 2 * PAD)]);
});
const linePath = computed(() => (pts.value.length ? 'M' + pts.value.map((p) => `${p[0].toFixed(1)},${p[1].toFixed(1)}`).join(' L') : ''));
const areaPath = computed(() => {
    if (!pts.value.length) return '';
    const last = pts.value[pts.value.length - 1];
    const first = pts.value[0];
    return `${linePath.value} L${last[0].toFixed(1)},${H - PAD} L${first[0].toFixed(1)},${H - PAD} Z`;
});
const lastPoint = computed(() => pts.value[pts.value.length - 1] ?? null);
</script>

<template>
    <Head title="Panel — przegląd" />

    <PanelLayout :user="user" :konto="konto">
        <header class="greet">
            <h1 class="greet__title">{{ greeting }}, {{ user.greetingName }}.</h1>
            <p class="greet__sub">Oto jak stoją Twoje ogłoszenia na Bear.</p>
        </header>

        <div class="overview">
            <div class="card portfolio">
                <div class="card__head">
                    <h2 class="card__title">Portfel ogłoszeń</h2>
                    <span class="card__total">{{ total }} łącznie</span>
                </div>
                <div v-if="total" class="bar" role="img" aria-label="Rozkład ogłoszeń według statusu">
                    <span
                        v-for="s in segments"
                        :key="s.key"
                        class="bar__seg"
                        :style="{ width: (grown ? pct(s.count) : 0) + '%', background: `var(${s.color})` }"
                        :title="`${STATUS_META[s.key].label}: ${s.count}`"
                    ></span>
                </div>
                <p v-else class="bar-empty">Nie masz jeszcze żadnych ogłoszeń.</p>
                <ul class="legend">
                    <li v-for="key in STATUS_ORDER" :key="key" class="legend__item">
                        <span class="legend__dot" :style="{ background: `var(${STATUS_META[key].color})` }"></span>
                        <span class="legend__label">{{ STATUS_META[key].label }}</span>
                        <span class="legend__count">{{ overview[key] ?? 0 }}</span>
                    </li>
                </ul>
            </div>

            <div class="card spark">
                <div class="card__head">
                    <h2 class="card__title">Aktywność</h2>
                    <span class="card__total">14 dni</span>
                </div>
                <svg class="spark__svg" :viewBox="`0 0 ${W} ${H}`" preserveAspectRatio="none" aria-hidden="true">
                    <line :x1="0" :y1="H - PAD" :x2="W" :y2="H - PAD" class="spark__base" />
                    <path v-if="areaPath" :d="areaPath" class="spark__area" />
                    <path v-if="linePath" :d="linePath" class="spark__line" />
                    <circle v-if="lastPoint" :cx="lastPoint[0]" :cy="lastPoint[1]" r="3" class="spark__dot" />
                </svg>
                <p class="spark__caption">
                    <strong>{{ activityTotal }}</strong>
                    {{ activityTotal === 1 ? 'nowe ogłoszenie' : 'nowych ogłoszeń' }}
                </p>
            </div>
        </div>

        <div class="callouts">
            <div class="callout">
                <span class="callout__num">{{ overview.total }}</span>
                <span class="callout__label">Wszystkie ogłoszenia</span>
            </div>
            <div v-if="overview.draft" class="callout callout--nudge">
                <span class="callout__num">{{ overview.draft }}</span>
                <span class="callout__label">Robocze — dokończ i opublikuj</span>
            </div>
            <div v-if="overview.inModeration" class="callout callout--alert">
                <span class="callout__num">{{ overview.inModeration }}</span>
                <span class="callout__label">W moderacji — sprawdź zgłoszenia</span>
            </div>
        </div>

        <div class="more">
            <AppButton :href="listingsHref" variant="secondary">Przejdź do ogłoszeń</AppButton>
        </div>
    </PanelLayout>
</template>

<style scoped>
.greet {
    margin-bottom: 1.4rem;
}
.greet__title {
    font-family: var(--font-display);
    font-weight: 800;
    font-size: clamp(1.7rem, 4vw, 2.6rem);
    letter-spacing: -0.035em;
    line-height: 1;
    margin: 0;
    text-wrap: balance;
}
.greet__sub {
    color: var(--text-soft);
    margin: 0.7rem 0 0;
}

.card {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: 1.3rem;
}
.card__head {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-bottom: 1.1rem;
}
.card__title {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 1.1rem;
    margin: 0;
}
.card__total {
    font-family: var(--font-mono);
    font-size: 0.78rem;
    color: var(--text-soft);
}

.overview {
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(0, 1fr);
    gap: 1.2rem;
}

/* Portfolio bar — the signature */
.bar {
    display: flex;
    height: 20px;
    border-radius: 5px;
    overflow: hidden;
    background: var(--surface-2);
    gap: 2px;
}
.bar__seg {
    height: 100%;
    min-width: 4px;
    transition: width 0.6s cubic-bezier(0.2, 0.9, 0.25, 1);
}
.bar-empty {
    color: var(--text-soft);
    font-size: 0.9rem;
    margin: 0;
}
.legend {
    list-style: none;
    margin: 1.1rem 0 0;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 0.6rem 1rem;
}
.legend__item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
}
.legend__dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    flex: none;
}
.legend__label {
    color: var(--text-soft);
}
.legend__count {
    margin-left: auto;
    font-family: var(--font-mono);
    font-weight: 700;
    font-variant-numeric: tabular-nums;
}

/* Sparkline */
.spark {
    display: flex;
    flex-direction: column;
}
.spark__svg {
    width: 100%;
    height: 62px;
    display: block;
}
.spark__base {
    stroke: var(--line);
    stroke-width: 1;
}
.spark__area {
    fill: color-mix(in srgb, var(--accent) 16%, transparent);
    stroke: none;
}
.spark__line {
    fill: none;
    stroke: var(--accent);
    stroke-width: 2;
    stroke-linejoin: round;
    stroke-linecap: round;
    vector-effect: non-scaling-stroke;
}
.spark__dot {
    fill: var(--accent);
}
.spark__caption {
    margin: 0.9rem 0 0;
    font-size: 0.85rem;
    color: var(--text-soft);
}
.spark__caption strong {
    font-family: var(--font-mono);
    color: var(--text);
    font-size: 1.05rem;
}

/* Callouts */
.callouts {
    display: flex;
    flex-wrap: wrap;
    gap: 0.9rem;
    margin-top: 1.2rem;
}
.callout {
    display: flex;
    align-items: baseline;
    gap: 0.6rem;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 6px;
    padding: 0.8rem 1rem;
    flex: 1 1 200px;
}
.callout__num {
    font-family: var(--font-mono);
    font-weight: 700;
    font-size: 1.5rem;
    font-variant-numeric: tabular-nums;
}
.callout__label {
    font-size: 0.85rem;
    color: var(--text-soft);
}
.callout--nudge {
    border-color: color-mix(in srgb, var(--accent) 45%, transparent);
}
.callout--nudge .callout__num {
    color: var(--accent-text);
}
.callout--alert {
    border-color: color-mix(in srgb, var(--status-hidden) 50%, transparent);
    background: color-mix(in srgb, var(--status-hidden) 8%, transparent);
}
.callout--alert .callout__num {
    color: var(--status-hidden);
}

.more {
    margin-top: 1.6rem;
}

@media (max-width: 900px) {
    .overview {
        grid-template-columns: 1fr;
    }
}
@media (prefers-reduced-motion: reduce) {
    .bar__seg {
        transition: none;
    }
}
</style>
