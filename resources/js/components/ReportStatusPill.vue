<script setup>
import { computed } from 'vue';
import { REPORT_STATUS_META } from '../lib/reportMeta';

/**
 * ReportStatusPill — a report's moderation status as a colored pill. Mirrors
 * StatusPill but for App\Enums\ReportStatus.
 */
const props = defineProps({
    status: { type: String, required: true }, // 'pending' | 'resolved' | 'dismissed'
});

const meta = computed(() => REPORT_STATUS_META[props.status] ?? { label: props.status, color: '--text-soft' });
</script>

<template>
    <span class="pill" :style="{ '--c': `var(${meta.color})` }">{{ meta.label }}</span>
</template>

<style scoped>
.pill {
    font-family: var(--font-mono);
    font-size: 0.66rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 0.28rem 0.6rem;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    white-space: nowrap;
    color: var(--c);
    background: color-mix(in srgb, var(--c) 14%, transparent);
    border: 1px solid color-mix(in srgb, var(--c) 34%, transparent);
}
.pill::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}
</style>
