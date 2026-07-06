<script setup>
import { ref, watch, nextTick } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { REPORT_REASONS } from '../lib/reportMeta';
import FormField from './FormField.vue';
import AppButton from './AppButton.vue';

/**
 * ReportDialog — a self-contained "report this" control. Renders a trigger
 * button that opens a modal with a reason + optional note, and POSTs to the
 * shared reports endpoint. Drop it in wherever a listing or account can be
 * reported; the flash toast confirms submission.
 */
const props = defineProps({
    type: { type: String, required: true }, // 'listing' | 'user'
    id: { type: [Number, String], required: true },
    subject: { type: String, default: null }, // human label shown in the heading
    label: { type: String, default: 'Zgłoś' },
});

const open = ref(false);
const dialog = ref(null);

const form = useForm({
    reportable_type: props.type,
    reportable_id: props.id,
    reason: '',
    description: '',
});

function show() {
    open.value = true;
    nextTick(() => dialog.value?.focus());
}

function close() {
    open.value = false;
    form.clearErrors();
}

function submit() {
    form.post('/zglos', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('reason', 'description');
            close();
        },
    });
}

watch(open, (isOpen) => {
    if (typeof document !== 'undefined') {
        document.body.style.overflow = isOpen ? 'hidden' : '';
    }
});
</script>

<template>
    <button type="button" class="trigger" @click="show">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M4 21V4h13l-1.5 4L17 12H4" />
        </svg>
        {{ label }}
    </button>

    <teleport to="body">
        <transition name="overlay">
            <div v-if="open" class="overlay" @click.self="close" @keydown.esc="close">
                <div
                    ref="dialog"
                    class="modal"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="report-title"
                    tabindex="-1"
                >
                    <h2 id="report-title" class="modal__title">Zgłoś naruszenie</h2>
                    <p v-if="subject" class="modal__subject">{{ subject }}</p>
                    <p class="modal__lead">
                        Powiedz nam, co jest nie tak. Zgłoszenie trafi do moderatorów Bear.
                    </p>

                    <form class="modal__form" @submit.prevent="submit">
                        <FormField label="Powód" for="report-reason" :error="form.errors.reason">
                            <select id="report-reason" v-model="form.reason" class="control">
                                <option value="" disabled>Wybierz powód…</option>
                                <option v-for="r in REPORT_REASONS" :key="r.value" :value="r.value">{{ r.label }}</option>
                            </select>
                        </FormField>

                        <FormField
                            label="Szczegóły (opcjonalnie)"
                            for="report-desc"
                            :error="form.errors.description"
                            hint="Dodaj kontekst, który pomoże nam ocenić zgłoszenie."
                        >
                            <textarea
                                id="report-desc"
                                v-model="form.description"
                                class="control"
                                rows="4"
                                maxlength="1000"
                                placeholder="Co dokładnie jest niezgodne z regulaminem?"
                            ></textarea>
                        </FormField>

                        <div class="modal__actions">
                            <AppButton variant="secondary" type="button" @click="close">Anuluj</AppButton>
                            <AppButton variant="primary" type="submit" :disabled="form.processing || !form.reason">
                                Wyślij zgłoszenie
                            </AppButton>
                        </div>
                    </form>
                </div>
            </div>
        </transition>
    </teleport>
</template>

<style scoped>
.trigger {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: none;
    border: 0;
    padding: 0;
    cursor: pointer;
    font-family: var(--font-mono);
    font-size: 0.74rem;
    letter-spacing: 0.03em;
    color: var(--text-soft);
    transition: color 0.15s ease;
}
.trigger:hover {
    color: var(--status-hidden);
}

.overlay {
    position: fixed;
    inset: 0;
    z-index: 120;
    display: grid;
    place-items: center;
    padding: 1rem;
    background: color-mix(in srgb, #000 55%, transparent);
    backdrop-filter: blur(2px);
}
.modal {
    width: min(460px, 100%);
    max-height: calc(100vh - 2rem);
    overflow-y: auto;
    background: var(--surface);
    border: 1px solid var(--line-strong);
    border-radius: 10px;
    padding: clamp(1.3rem, 4vw, 1.8rem);
    box-shadow: 0 24px 60px -20px rgba(0, 0, 0, 0.55);
}
.modal:focus {
    outline: none;
}
.modal__title {
    font-family: var(--font-display);
    font-weight: 800;
    font-size: 1.4rem;
    letter-spacing: -0.02em;
    margin: 0;
}
.modal__subject {
    font-family: var(--font-mono);
    font-size: 0.78rem;
    color: var(--text-soft);
    margin: 0.4rem 0 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.modal__lead {
    color: var(--text-soft);
    font-size: 0.9rem;
    margin: 0.7rem 0 1.2rem;
}
.modal__form {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
}
.control {
    width: 100%;
    font-family: var(--font-sans);
    font-size: 0.95rem;
    color: var(--text);
    background: var(--bg);
    border: 1.5px solid var(--line-strong);
    border-radius: 5px;
    padding: 0.6rem 0.7rem;
}
.control:focus {
    outline: none;
    border-color: var(--accent);
}
textarea.control {
    resize: vertical;
    min-height: 84px;
}
.modal__actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.7rem;
    margin-top: 0.2rem;
}

.overlay-enter-active,
.overlay-leave-active {
    transition: opacity 0.2s ease;
}
.overlay-enter-from,
.overlay-leave-to {
    opacity: 0;
}
@media (prefers-reduced-motion: reduce) {
    .overlay,
    .trigger {
        transition: none;
    }
}
</style>
