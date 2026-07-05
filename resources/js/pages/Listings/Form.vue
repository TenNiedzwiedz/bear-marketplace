<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import SiteHeader from '../../components/SiteHeader.vue';
import SiteFooter from '../../components/SiteFooter.vue';
import AppButton from '../../components/AppButton.vue';
import FormField from '../../components/FormField.vue';

const props = defineProps({
    listing: { type: Object, default: null },
    categories: { type: Array, default: () => [] }, // [{ label, options: [{ value, label }] }]
});

const isEdit = computed(() => !!props.listing);

const form = useForm({
    title: props.listing?.title ?? '',
    category_id: props.listing?.category_id ?? '',
    price: props.listing?.price ?? '',
    is_negotiable: props.listing?.is_negotiable ?? false,
    location: props.listing?.location ?? '',
    description: props.listing?.description ?? '',
    images: [],
    remove_images: [],
    action: 'publish',
});

// Existing images (edit) with a remove toggle.
const existing = ref((props.listing?.images ?? []).map((image) => ({ ...image, remove: false })));
function toggleRemove(image) {
    image.remove = !image.remove;
    form.remove_images = existing.value.filter((i) => i.remove).map((i) => i.id);
}

// New image previews.
const previews = ref([]);
function onFiles(event) {
    const files = Array.from(event.target.files);
    form.images = files;
    previews.value.forEach((url) => URL.revokeObjectURL(url));
    previews.value = files.map((file) => URL.createObjectURL(file));
}

function submit(action) {
    form.action = action;
    const options = { preserveScroll: true, forceFormData: true };

    if (isEdit.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(`/ogloszenia/${props.listing.id}`, options);
    } else {
        form.post('/ogloszenia', options);
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Edytuj ogłoszenie' : 'Wystaw ogłoszenie'" />

    <SiteHeader />

    <main class="wrap">
        <nav class="crumbs" aria-label="Ścieżka">
            <Link href="/ogloszenia">Ogłoszenia</Link>
            <span class="crumbs__sep" aria-hidden="true">›</span>
            <span>{{ isEdit ? 'Edycja' : 'Nowe ogłoszenie' }}</span>
        </nav>

        <header class="head">
            <h1 class="head__title">{{ isEdit ? 'Edytuj ogłoszenie' : 'Wystaw ogłoszenie' }}</h1>
            <p class="head__lede">Dobre zdjęcia i konkretny opis sprzedają najszybciej. Wystawienie jest darmowe.</p>
        </header>

        <form class="form" @submit.prevent="submit('publish')">
            <FormField label="Tytuł" for="f-title" :error="form.errors.title">
                <input
                    id="f-title"
                    v-model="form.title"
                    class="input"
                    type="text"
                    maxlength="120"
                    placeholder="np. Rower górski Kross Level 5.0, rozm. L"
                />
            </FormField>

            <FormField label="Kategoria" for="f-category" :error="form.errors.category_id">
                <select id="f-category" v-model="form.category_id" class="input select">
                    <option value="" disabled>Wybierz kategorię</option>
                    <optgroup v-for="group in categories" :key="group.label" :label="group.label">
                        <option v-for="option in group.options" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </optgroup>
                </select>
            </FormField>

            <div class="row">
                <FormField
                    label="Cena (zł)"
                    for="f-price"
                    :error="form.errors.price"
                    hint="Zostaw puste, jeśli cena do uzgodnienia."
                >
                    <input
                        id="f-price"
                        v-model="form.price"
                        class="input"
                        type="number"
                        min="0"
                        step="0.01"
                        inputmode="decimal"
                        placeholder="np. 450"
                    />
                </FormField>
                <label class="check">
                    <input v-model="form.is_negotiable" type="checkbox" />
                    <span>Do negocjacji</span>
                </label>
            </div>

            <FormField label="Lokalizacja" for="f-location" :error="form.errors.location">
                <input
                    id="f-location"
                    v-model="form.location"
                    class="input"
                    type="text"
                    maxlength="120"
                    placeholder="np. Kraków, Podgórze"
                />
            </FormField>

            <FormField
                label="Opis"
                for="f-description"
                :error="form.errors.description"
                hint="Opisz stan, wymiary i powód sprzedaży."
            >
                <textarea id="f-description" v-model="form.description" class="input textarea" rows="7" maxlength="5000"></textarea>
            </FormField>

            <FormField
                label="Zdjęcia"
                :error="form.errors.images || form.errors['images.0']"
                hint="Do 8 zdjęć (JPG, PNG lub WEBP), maks. 5 MB każde."
            >
                <div v-if="existing.length" class="thumbs">
                    <div v-for="image in existing" :key="image.id" class="thumb" :class="{ 'is-removed': image.remove }">
                        <img :src="image.url" alt="" />
                        <button type="button" class="thumb__btn" @click="toggleRemove(image)">
                            {{ image.remove ? 'Przywróć' : 'Usuń' }}
                        </button>
                    </div>
                </div>

                <label class="file">
                    <input type="file" accept="image/jpeg,image/png,image/webp" multiple @change="onFiles" />
                    <span>Wybierz zdjęcia…</span>
                </label>

                <div v-if="previews.length" class="thumbs">
                    <div v-for="(url, i) in previews" :key="i" class="thumb">
                        <img :src="url" alt="" />
                    </div>
                </div>
            </FormField>

            <div class="actions">
                <AppButton type="submit" variant="primary" :disabled="form.processing">
                    {{ isEdit ? 'Zapisz i opublikuj' : 'Opublikuj ogłoszenie' }}
                </AppButton>
                <AppButton type="button" variant="secondary" :disabled="form.processing" @click="submit('draft')">
                    Zapisz jako robocze
                </AppButton>
                <Link :href="isEdit ? `/ogloszenia/${listing.id}` : '/ogloszenia'" class="cancel">Anuluj</Link>
                <span v-if="form.processing" class="saving" role="status">Zapisywanie…</span>
            </div>
        </form>
    </main>

    <SiteFooter />
</template>

<style scoped>
.wrap {
    max-width: 680px;
    margin: 0 auto;
    padding: clamp(1.4rem, 4vw, 2.6rem) clamp(1rem, 4vw, 2.4rem);
}
.crumbs {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
    font-family: var(--font-mono);
    font-size: 0.74rem;
    color: var(--text-soft);
    margin-bottom: 1.3rem;
}
.crumbs a {
    color: var(--text-soft);
    text-decoration: none;
}
.crumbs a:hover {
    color: var(--accent-text);
}
.crumbs__sep {
    opacity: 0.6;
}
.head {
    margin-bottom: clamp(1.6rem, 4vw, 2.2rem);
}
.head__title {
    font-family: var(--font-display);
    font-weight: 800;
    font-size: clamp(1.8rem, 5vw, 2.6rem);
    letter-spacing: -0.035em;
    line-height: 1;
    margin: 0;
    text-wrap: balance;
}
.head__lede {
    color: var(--text-soft);
    margin: 0.8rem 0 0;
    max-width: 52ch;
}

.form {
    display: flex;
    flex-direction: column;
    gap: 1.4rem;
}

/* Inputs */
.input {
    font-family: var(--font-sans);
    font-size: 1rem;
    color: var(--text);
    background: var(--surface);
    border: 1.5px solid var(--line-strong);
    border-radius: 5px;
    padding: 0.7rem 0.85rem;
    width: 100%;
}
.input::placeholder {
    color: var(--text-soft);
}
.input:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 28%, transparent);
}
.textarea {
    resize: vertical;
    min-height: 120px;
    line-height: 1.6;
}
.select {
    appearance: none;
    background-image: linear-gradient(45deg, transparent 50%, var(--text-soft) 50%),
        linear-gradient(135deg, var(--text-soft) 50%, transparent 50%);
    background-position: calc(100% - 18px) center, calc(100% - 13px) center;
    background-size: 5px 5px, 5px 5px;
    background-repeat: no-repeat;
    padding-right: 2.2rem;
}

.row {
    display: flex;
    align-items: flex-start;
    gap: 1.2rem;
    flex-wrap: wrap;
}
.row > :first-child {
    flex: 1 1 220px;
}
.check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.92rem;
    padding-top: 2.1rem;
    white-space: nowrap;
}
.check input {
    width: 18px;
    height: 18px;
    accent-color: var(--accent);
}

/* Images */
.thumbs {
    display: flex;
    flex-wrap: wrap;
    gap: 0.7rem;
}
.thumb {
    position: relative;
    width: 92px;
    height: 74px;
    border-radius: 5px;
    overflow: hidden;
    border: 1px solid var(--line);
}
.thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.thumb.is-removed img {
    opacity: 0.35;
    filter: grayscale(1);
}
.thumb__btn {
    position: absolute;
    inset: auto 0 0 0;
    font-family: var(--font-mono);
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    border: 0;
    cursor: pointer;
    padding: 0.28rem;
    background: color-mix(in srgb, var(--pine) 78%, transparent);
    color: #fff;
}
.file {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: 1.5px dashed var(--line-strong);
    border-radius: 5px;
    padding: 0.8rem 1rem;
    cursor: pointer;
    color: var(--text-soft);
    font-weight: 600;
    font-size: 0.92rem;
    width: fit-content;
}
.file:hover {
    border-color: var(--accent);
    color: var(--text);
}
.file input {
    position: absolute;
    width: 1px;
    height: 1px;
    overflow: hidden;
    clip: rect(0 0 0 0);
}

/* Actions */
.actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.9rem;
    padding-top: 0.6rem;
    border-top: 1px solid var(--line);
    margin-top: 0.4rem;
}
.cancel {
    font-size: 0.92rem;
    font-weight: 600;
    color: var(--text-soft);
    text-decoration: none;
}
.cancel:hover {
    color: var(--text);
}
.saving {
    font-family: var(--font-mono);
    font-size: 0.78rem;
    color: var(--text-soft);
}
</style>
