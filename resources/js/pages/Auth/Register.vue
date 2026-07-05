<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '../../components/AuthLayout.vue';
import AppButton from '../../components/AppButton.vue';
import FormField from '../../components/FormField.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    account_type: 'private',
    company_name: '',
    tax_id: '',
    city: '',
    address_line: '',
});

const isCompany = computed(() => form.account_type === 'company');

const accountTypes = [
    { value: 'private', label: 'Osoba prywatna' },
    { value: 'company', label: 'Firma' },
];

function submit() {
    form.post('/rejestracja', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Załóż konto" />

    <AuthLayout title="Załóż konto" subtitle="Wystawiaj ogłoszenia i rozmawiaj z kupującymi.">
        <form class="form" @submit.prevent="submit">
            <FormField label="Typ konta" :error="form.errors.account_type">
                <div class="segmented" role="group" aria-label="Typ konta">
                    <button
                        v-for="opt in accountTypes"
                        :key="opt.value"
                        type="button"
                        class="segmented__btn"
                        :class="{ 'is-active': form.account_type === opt.value }"
                        :aria-pressed="form.account_type === opt.value"
                        @click="form.account_type = opt.value"
                    >
                        {{ opt.label }}
                    </button>
                </div>
            </FormField>

            <FormField :label="isCompany ? 'Osoba kontaktowa' : 'Imię i nazwisko'" for="name" :error="form.errors.name">
                <input id="name" v-model="form.name" class="input" type="text" autocomplete="name" required />
            </FormField>

            <template v-if="isCompany">
                <FormField label="Nazwa firmy" for="company_name" :error="form.errors.company_name">
                    <input id="company_name" v-model="form.company_name" class="input" type="text" required />
                </FormField>
                <div class="row">
                    <FormField label="NIP" for="tax_id" :error="form.errors.tax_id">
                        <input id="tax_id" v-model="form.tax_id" class="input" type="text" inputmode="numeric" required />
                    </FormField>
                    <FormField label="Miasto" for="city" :error="form.errors.city" hint="Opcjonalne.">
                        <input id="city" v-model="form.city" class="input" type="text" autocomplete="address-level2" />
                    </FormField>
                </div>
            </template>

            <FormField label="E-mail" for="email" :error="form.errors.email">
                <input id="email" v-model="form.email" class="input" type="email" autocomplete="email" required />
            </FormField>

            <div class="row">
                <FormField label="Hasło" for="password" :error="form.errors.password" hint="Min. 8 znaków.">
                    <input id="password" v-model="form.password" class="input" type="password" autocomplete="new-password" required />
                </FormField>
                <FormField label="Powtórz hasło" for="password_confirmation">
                    <input id="password_confirmation" v-model="form.password_confirmation" class="input" type="password" autocomplete="new-password" required />
                </FormField>
            </div>

            <AppButton type="submit" variant="primary" class="submit" :disabled="form.processing">
                Załóż konto
            </AppButton>

            <p class="alt">Masz już konto? <Link href="/logowanie">Zaloguj się</Link></p>
        </form>
    </AuthLayout>
</template>

<style scoped>
.form {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
    margin-top: 1.6rem;
}
.input {
    font-family: var(--font-sans);
    font-size: 1rem;
    color: var(--text);
    background: var(--bg);
    border: 1.5px solid var(--line-strong);
    border-radius: 5px;
    padding: 0.7rem 0.85rem;
    width: 100%;
}
.input:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 28%, transparent);
}
.row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
.segmented {
    display: flex;
    border: 1.5px solid var(--line-strong);
    border-radius: 5px;
    padding: 3px;
    gap: 3px;
}
.segmented__btn {
    flex: 1;
    font-family: var(--font-sans);
    font-size: 0.92rem;
    font-weight: 600;
    background: transparent;
    border: 0;
    border-radius: 3px;
    padding: 0.55rem;
    color: var(--text-soft);
    cursor: pointer;
}
.segmented__btn.is-active {
    background: var(--accent);
    color: var(--accent-ink);
}
.submit {
    width: 100%;
    justify-content: center;
    margin-top: 0.3rem;
}
.alt {
    font-size: 0.9rem;
    color: var(--text-soft);
    margin: 0;
    text-align: center;
}
.alt a {
    color: var(--accent-text);
    font-weight: 600;
}
@media (max-width: 440px) {
    .row {
        grid-template-columns: 1fr;
    }
}
</style>
