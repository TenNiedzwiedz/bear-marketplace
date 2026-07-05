<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '../../components/AuthLayout.vue';
import AppButton from '../../components/AppButton.vue';
import FormField from '../../components/FormField.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/logowanie', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Zaloguj się" />

    <AuthLayout title="Zaloguj się" subtitle="Wróć do swoich ogłoszeń i wiadomości.">
        <form class="form" @submit.prevent="submit">
            <FormField label="E-mail" for="email" :error="form.errors.email">
                <input id="email" v-model="form.email" class="input" type="email" autocomplete="email" required />
            </FormField>

            <FormField label="Hasło" for="password" :error="form.errors.password">
                <input id="password" v-model="form.password" class="input" type="password" autocomplete="current-password" required />
            </FormField>

            <label class="remember">
                <input v-model="form.remember" type="checkbox" />
                <span>Nie wylogowuj mnie</span>
            </label>

            <AppButton type="submit" variant="primary" class="submit" :disabled="form.processing">
                Zaloguj się
            </AppButton>

            <p class="alt">Nie masz konta? <Link href="/rejestracja">Załóż konto</Link></p>
        </form>
    </AuthLayout>
</template>

<style scoped>
.form {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
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
.remember {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: var(--text-soft);
}
.remember input {
    width: 17px;
    height: 17px;
    accent-color: var(--accent);
}
.submit {
    width: 100%;
    justify-content: center;
    margin-top: 0.2rem;
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
</style>
