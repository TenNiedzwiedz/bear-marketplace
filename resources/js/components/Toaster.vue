<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

/**
 * Toaster — a global flash-message stack. Mounted once at the app root, it shows
 * a toast whenever a response carries `flash.success` / `flash.error`, then
 * auto-dismisses. Client-only: renders nothing during SSR.
 */
const page = usePage();
const toasts = ref([]);
let seq = 0;
let stopListening;

function add(type, message) {
    const id = ++seq;
    toasts.value.push({ id, type, message });
    setTimeout(() => dismiss(id), 4500);
}

function dismiss(id) {
    toasts.value = toasts.value.filter((t) => t.id !== id);
}

function fromFlash(flash) {
    if (!flash) return;
    if (flash.success) add('success', flash.success);
    if (flash.error) add('error', flash.error);
}

onMounted(() => {
    fromFlash(page.props.flash); // flash present on a full-page redirect
    stopListening = router.on('success', (event) => fromFlash(event.detail.page.props.flash));
});

onUnmounted(() => stopListening && stopListening());
</script>

<template>
    <transition-group name="toast" tag="div" class="toaster" aria-live="polite">
        <div v-for="t in toasts" :key="t.id" class="toast" :class="`toast--${t.type}`" role="status">
            <span class="toast__icon" aria-hidden="true">
                <svg v-if="t.type === 'success'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M8.5 12.4l2.4 2.4 4.6-5" />
                </svg>
                <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M12 7.8v4.6" />
                    <path d="M12 16h.01" />
                </svg>
            </span>
            <span class="toast__msg">{{ t.message }}</span>
            <button type="button" class="toast__close" aria-label="Zamknij" @click="dismiss(t.id)">×</button>
        </div>
    </transition-group>
</template>

<style scoped>
.toaster {
    position: fixed;
    right: 1.4rem;
    bottom: 1.4rem;
    z-index: 100;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    width: min(380px, calc(100vw - 2rem));
    pointer-events: none;
}
.toast {
    pointer-events: auto;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    background: var(--surface);
    color: var(--text);
    border: 1px solid var(--line-strong);
    border-left: 4px solid var(--status-active);
    border-radius: 6px;
    padding: 0.8rem 1rem;
    box-shadow: var(--shadow);
}
.toast--error {
    border-left-color: var(--status-hidden);
}
.toast__icon {
    flex: none;
    display: grid;
    place-items: center;
    color: var(--status-active);
}
.toast__icon svg {
    width: 22px;
    height: 22px;
}
.toast--error .toast__icon {
    color: var(--status-hidden);
}
.toast__msg {
    flex: 1;
    font-size: 0.92rem;
}
.toast__close {
    background: none;
    border: 0;
    cursor: pointer;
    color: var(--text-soft);
    font-size: 1.25rem;
    line-height: 1;
    padding: 0 0 0.1rem;
}
.toast__close:hover {
    color: var(--text);
}

.toast-enter-active,
.toast-leave-active {
    transition: opacity 0.25s ease, transform 0.25s ease;
}
.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateX(20px);
}
@media (prefers-reduced-motion: reduce) {
    .toast-enter-active,
    .toast-leave-active {
        transition: none;
    }
    .toast-enter-from,
    .toast-leave-to {
        transform: none;
    }
}
</style>
