<script setup>
/**
 * CategoryGlyph — the marketplace's monoline icon set, one coherent stroke
 * style. Referenced by name so tiles, listing thumbnails and steps stay visually
 * consistent. Add a category by adding a branch below.
 */
defineProps({
    name: { type: String, required: true },
});
</script>

<template>
    <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
    >
        <template v-if="name === 'motoryzacja'">
            <path d="M4 13l1.7-4.3A2.4 2.4 0 0 1 8 7.2h8a2.4 2.4 0 0 1 2.3 1.5L20 13" />
            <path d="M3 13h18v4h-2.2" />
            <path d="M7.2 17H3v-4" />
            <circle cx="8" cy="17" r="1.9" />
            <circle cx="16" cy="17" r="1.9" />
        </template>
        <template v-else-if="name === 'rowery'">
            <circle cx="6" cy="16" r="3.4" />
            <circle cx="18" cy="16" r="3.4" />
            <path d="M6 16l4.5-6.5H15" />
            <path d="M10.5 9.5l4 6.5" />
            <path d="M9 9.5h3.5" />
        </template>
        <template v-else-if="name === 'elektronika'">
            <rect x="7" y="3" width="10" height="18" rx="2.2" />
            <path d="M10.5 5.8h3" />
            <path d="M12 18.2h.01" />
        </template>
        <template v-else-if="name === 'dom'">
            <path d="M4 11l8-6 8 6" />
            <path d="M6 10v9h12v-9" />
            <path d="M10 19v-5h4v5" />
        </template>
        <template v-else-if="name === 'moda'">
            <path d="M8.6 4L4 7l1.9 2.7L8 8.3V20h8V8.3l2.1 1.4L20 7l-4.6-3a3.4 3.4 0 0 1-6.8 0z" />
        </template>
        <template v-else-if="name === 'sport'">
            <circle cx="12" cy="12" r="8" />
            <path d="M12 4a9 9 0 0 0 0 16" />
            <path d="M12 4a9 9 0 0 1 0 16" />
        </template>
        <template v-else-if="name === 'dzieci'">
            <circle cx="12" cy="8" r="5" />
            <path d="M12 13v5" />
            <path d="M10.6 18h2.8" />
        </template>
        <template v-else-if="name === 'zwierzeta'">
            <circle cx="7" cy="11" r="1.5" />
            <circle cx="10.5" cy="8.8" r="1.5" />
            <circle cx="13.5" cy="8.8" r="1.5" />
            <circle cx="17" cy="11" r="1.5" />
            <path d="M8.5 16.4c0-2.1 1.6-3.6 3.5-3.6s3.5 1.5 3.5 3.6c0 1.6-1.6 2.3-3.5 2.3s-3.5-.7-3.5-2.3z" />
        </template>
        <template v-else-if="name === 'nieruchomosci'">
            <path d="M5 20V9l7-5 7 5v11" />
            <path d="M5 20h14" />
            <path d="M10 20v-5h4v5" />
            <path d="M9 11h1.5M13.5 11H15" />
        </template>
        <template v-else-if="name === 'uslugi'">
            <path d="M14.5 5.5a3.5 3.5 0 0 0-4.8 4.3l-5 5a1.7 1.7 0 0 0 2.4 2.4l5-5a3.5 3.5 0 0 0 4.3-4.8l-2.1 2.1-1.9-.4-.4-1.9z" />
        </template>
        <!-- Fallback so an unmapped category never renders blank. -->
        <template v-else>
            <path d="M4 8.5A2.5 2.5 0 0 1 6.5 6h5l8 8-5.5 5.5-8-8z" />
            <circle cx="9" cy="11" r="1.3" />
        </template>
    </svg>
</template>
