<script setup>
import BearSeal from './BearSeal.vue';
import SellerBadge from './SellerBadge.vue';

/**
 * ListingCard — one ogłoszenie. The composition where the whole system meets:
 * price tag, trust seal, and a seller badge whose colour reads at a glance.
 * Pass an `image` URL for the photo, or use the #thumb slot / branded default.
 */
defineProps({
    category: { type: String, required: true },
    title: { type: String, required: true },
    price: { type: String, required: true },
    location: { type: String, required: true },
    postedAt: { type: String, default: null },
    sellerName: { type: String, required: true },
    sellerType: { type: String, required: true }, // 'firma' | 'prywatna'
    code: { type: String, default: null },
    image: { type: String, default: null },
    thumbBackground: {
        type: String,
        default: 'linear-gradient(135deg, #2b3d33, #c9821f)',
    },
});
</script>

<template>
    <article class="card">
        <div class="card__thumb" :style="{ background: thumbBackground }">
            <img v-if="image" :src="image" :alt="title" class="card__photo" loading="lazy" />
            <slot v-else name="thumb" />
            <span class="card__price">{{ price }}</span>
            <BearSeal class="card__stamp" :size="46" :ring="false" aria-hidden="true" />
        </div>
        <div class="card__body">
            <p class="card__cat">{{ category }}</p>
            <h3 class="card__title">{{ title }}</h3>
            <p class="card__meta">{{ location }}<template v-if="postedAt"> · {{ postedAt }}</template></p>
            <div class="card__foot">
                <div class="card__seller">
                    <SellerBadge :type="sellerType" />
                    <span class="card__seller-name">{{ sellerName }}</span>
                </div>
                <span v-if="code" class="card__code">{{ code }}</span>
            </div>
        </div>
    </article>
</template>

<style scoped>
.card {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 6px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: var(--shadow);
    transition: transform 0.16s ease;
}
.card:hover {
    transform: translateY(-4px);
}
.card__thumb {
    position: relative;
    aspect-ratio: 4 / 3;
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom: 1px solid var(--line);
    color: rgba(255, 255, 255, 0.9);
}
.card__thumb :slotted(svg) {
    width: 44%;
    height: 44%;
}
.card__photo {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.card__stamp {
    position: absolute;
    right: 10px;
    bottom: 10px;
    color: #fff;
    opacity: 0.92;
    transform: rotate(-8deg);
}
.card__price {
    position: absolute;
    left: 12px;
    top: 12px;
    font-family: var(--font-mono);
    font-weight: 700;
    font-size: 1rem;
    background: var(--accent);
    color: var(--accent-ink);
    padding: 0.3rem 0.55rem;
    border-radius: 3px;
    box-shadow: 0 6px 14px -8px rgba(0, 0, 0, 0.6);
    font-variant-numeric: tabular-nums;
}
.card__body {
    padding: 1.1rem 1.15rem 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
    flex: 1;
}
.card__cat {
    font-family: var(--font-mono);
    font-size: 0.66rem;
    font-weight: 700;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--text-soft);
    margin: 0;
}
.card__title {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 1.12rem;
    line-height: 1.15;
    letter-spacing: -0.015em;
    margin: 0;
}
.card__meta {
    font-family: var(--font-mono);
    font-size: 0.72rem;
    color: var(--text-soft);
    letter-spacing: 0.04em;
    margin: 0;
}
.card__foot {
    margin-top: auto;
    padding-top: 0.9rem;
    border-top: 1px solid var(--line);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.6rem;
}
.card__seller {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    align-items: flex-start;
}
.card__seller-name {
    font-weight: 600;
    font-size: 0.9rem;
}
.card__code {
    font-family: var(--font-mono);
    font-size: 0.66rem;
    color: var(--text-soft);
    letter-spacing: 0.08em;
}
@media (prefers-reduced-motion: reduce) {
    .card {
        transition: none;
    }
    .card:hover {
        transform: none;
    }
}
</style>
