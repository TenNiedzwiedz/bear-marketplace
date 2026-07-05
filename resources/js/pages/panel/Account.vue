<script setup>
import { Head } from '@inertiajs/vue3';
import PanelLayout from '../../components/PanelLayout.vue';
import AppButton from '../../components/AppButton.vue';
import SellerBadge from '../../components/SellerBadge.vue';

defineProps({
    user: { type: Object, required: true },
});
</script>

<template>
    <Head title="Panel — konto" />

    <PanelLayout :user="user" title="Konto">
        <div class="account">
            <div class="card">
                <h2 class="card__subtitle">Dane konta</h2>
                <dl class="facts">
                    <div class="facts__row"><dt>Nazwa</dt><dd>{{ user.name }}</dd></div>
                    <div class="facts__row"><dt>E-mail</dt><dd>{{ user.email }}</dd></div>
                    <div class="facts__row"><dt>Typ konta</dt><dd><SellerBadge :type="user.type" /></dd></div>
                    <div class="facts__row"><dt>Na Bear od</dt><dd>{{ user.memberSince }}</dd></div>
                </dl>
                <AppButton variant="secondary" type="button">Ustawienia konta</AppButton>
            </div>

            <div v-if="user.company" class="card">
                <h2 class="card__subtitle">Profil firmy</h2>
                <dl class="facts">
                    <div class="facts__row"><dt>Firma</dt><dd>{{ user.company.name }}</dd></div>
                    <div v-if="user.company.taxId" class="facts__row"><dt>NIP</dt><dd class="mono">{{ user.company.taxId }}</dd></div>
                    <div v-if="user.company.address" class="facts__row"><dt>Adres</dt><dd>{{ user.company.address }}</dd></div>
                    <div v-if="user.company.city" class="facts__row"><dt>Miasto</dt><dd>{{ user.company.city }}</dd></div>
                </dl>
                <AppButton variant="secondary" type="button">Edytuj profil firmy</AppButton>
            </div>

            <div v-else class="card card--prompt">
                <h2 class="card__subtitle">Sprzedajesz jako firma?</h2>
                <p class="prompt__text">Dodaj profil firmy, żeby przy Twoich ogłoszeniach pojawiła się odznaka „Firma" i dane kontaktowe.</p>
                <AppButton variant="secondary" type="button">Dodaj profil firmy</AppButton>
            </div>
        </div>
    </PanelLayout>
</template>

<style scoped>
.account {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.2rem;
    align-items: start;
}
.card {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: 1.3rem;
}
.card--prompt {
    border-style: dashed;
    border-color: var(--line-strong);
}
.card__subtitle {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--text-soft);
    margin: 0 0 1rem;
}
.prompt__text {
    color: var(--text-soft);
    font-size: 0.9rem;
    margin: 0 0 1rem;
}
.mono {
    font-family: var(--font-mono);
}
.facts {
    margin: 0 0 1.1rem;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}
.facts__row {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    font-size: 0.88rem;
}
.facts__row dt {
    color: var(--text-soft);
}
.facts__row dd {
    margin: 0;
    text-align: right;
    font-weight: 500;
}
</style>
