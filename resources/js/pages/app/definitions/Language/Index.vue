<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/app/common/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import DefinitionsLayout from '@/pages/app/definitions/partials/Layout.vue';
import { index as languageRoute } from '@/routes/settings/definitions/languages';
import { type BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Tanımlamalar', href: '#' },
    { title: 'Diller', href: languageRoute().url },
];
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Diller" />

        <DefinitionsLayout>
            <div class="space-y-6">
                <div
                    class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
                >
                    <Heading
                        variant="small"
                        title="Diller"
                        description="Sistem üzerinde kullanılan diller."
                    />

                    <span>Diller</span>
                </div>
            </div>
        </DefinitionsLayout>
    </AppLayout>
</template>
