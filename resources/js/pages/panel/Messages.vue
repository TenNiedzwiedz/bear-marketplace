<script setup>
import { Head } from '@inertiajs/vue3';
import PanelLayout from '../../components/PanelLayout.vue';
import AppTag from '../../components/AppTag.vue';
import EmptyState from '../../components/EmptyState.vue';

defineProps({
    user: { type: Object, required: true },
    konto: { type: String, default: null },
});
</script>

<template>
    <Head title="Panel — wiadomości" />

    <PanelLayout :user="user" :konto="konto" title="Wiadomości">
        <template #actions>
            <AppTag>Wkrótce</AppTag>
        </template>

        <EmptyState
            title="Tu porozmawiasz z kupującymi"
            text="Gdy ktoś napisze w sprawie ogłoszenia, rozmowa pojawi się w tym miejscu."
        />
    </PanelLayout>
</template>
