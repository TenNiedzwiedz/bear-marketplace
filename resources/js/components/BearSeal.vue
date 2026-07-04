<script setup>
/**
 * BearSeal — the brand's stamped emblem.
 * Use `ring` for the full lettered seal (masthead, empty states); drop it
 * for the compact emblem used as a trust mark on cards and avatars.
 */
defineProps({
    size: { type: [Number, String], default: 240 },
    ring: { type: Boolean, default: true },
    ringText: {
        type: String,
        default: 'BEAR · MARKETPLACE · ZAUFANY HANDEL · EST. 2026 · ',
    },
    label: { type: String, default: 'Pieczęć Bear Marketplace' },
});
</script>

<template>
    <svg
        class="seal"
        :width="size"
        :height="size"
        viewBox="0 0 240 240"
        role="img"
        :aria-label="label"
    >
        <defs>
            <path id="bearSealRing" d="M120,120 m-96,0 a96,96 0 1,1 192,0 a96,96 0 1,1 -192,0" />
        </defs>
        <template v-if="ring">
            <circle cx="120" cy="120" r="112" fill="none" stroke="currentColor" stroke-width="2" />
            <circle cx="120" cy="120" r="102" fill="none" stroke="currentColor" stroke-width="1" opacity=".5" />
            <text class="seal__ring">
                <textPath href="#bearSealRing" startOffset="0">{{ ringText }}</textPath>
            </text>
        </template>
        <circle v-else cx="120" cy="120" r="112" fill="none" stroke="currentColor" stroke-width="8" />

        <g transform="translate(120 128)">
            <circle cx="-38" cy="-40" r="20" fill="currentColor" />
            <circle cx="38" cy="-40" r="20" fill="currentColor" />
            <circle cx="-38" cy="-40" r="8" fill="var(--seal-muzzle)" />
            <circle cx="38" cy="-40" r="8" fill="var(--seal-muzzle)" />
            <path d="M-58,-6 a58,54 0 1,1 116,0 a58,50 0 0,1 -58,52 a58,50 0 0,1 -58,-52 z" fill="currentColor" />
            <ellipse cx="0" cy="26" rx="30" ry="26" fill="var(--seal-muzzle)" />
            <circle cx="-22" cy="-4" r="6.5" fill="var(--seal-muzzle)" />
            <circle cx="22" cy="-4" r="6.5" fill="var(--seal-muzzle)" />
            <path d="M-13,18 q13,14 26,0" fill="none" stroke="currentColor" stroke-width="4.5" stroke-linecap="round" />
            <path d="M0,18 v10" fill="none" stroke="currentColor" stroke-width="4.5" stroke-linecap="round" />
            <ellipse cx="0" cy="10" rx="10" ry="7.5" fill="currentColor" />
        </g>
    </svg>
</template>

<style scoped>
.seal {
    color: var(--seal);
    display: block;
}
.seal__ring {
    font-family: var(--font-mono);
    font-size: 8.4px;
    font-weight: 700;
    letter-spacing: 0.34em;
    fill: currentColor;
    text-transform: uppercase;
}
</style>
