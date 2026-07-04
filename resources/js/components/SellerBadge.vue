<script setup>
import { computed } from 'vue';

/**
 * SellerBadge — encodes who is selling. Colour is meaning, not decoration:
 * moss = osoba prywatna, żurawina = firma. Reuse everywhere a listing shows its seller.
 */
const props = defineProps({
    type: {
        type: String,
        required: true,
        validator: (v) => ['firma', 'prywatna'].includes(v),
    },
    label: { type: String, default: null },
});

const defaultLabel = computed(() => (props.type === 'firma' ? 'Firma' : 'Osoba prywatna'));
</script>

<template>
    <span class="badge" :class="`badge--${type}`">{{ label ?? defaultLabel }}</span>
</template>

<style scoped>
.badge {
    font-family: var(--font-mono);
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    padding: 0.34rem 0.6rem;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    border: 1.5px solid;
    white-space: nowrap;
}
.badge::before {
    content: '';
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: currentColor;
}
.badge--firma {
    color: var(--seller-firma);
}
.badge--prywatna {
    color: var(--seller-priv);
}
</style>
