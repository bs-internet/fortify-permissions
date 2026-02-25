<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/app/common/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import DefinitionsLayout from '@/pages/app/definitions/partials/Layout.vue';
import { index as currencyRoute } from '@/routes/settings/definitions/currencies';
import { type BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Tanımlamalar', href: '#' },
    { title: 'Para Birimleri', href: currencyRoute().url },
];
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Para Birimleri" />

        <DefinitionsLayout>
            <div class="space-y-6">
                <div
                    class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
                >
                    <Heading
                        variant="small"
                        title="Para Birimleri"
                        description="Sistem üzerinde kullanılan para birimleri."
                    />

                    <span>Para Birimleri</span>
                </div>
            </div>
        </DefinitionsLayout>
    </AppLayout>
</template>
