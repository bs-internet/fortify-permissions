<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppearanceTabs from '@/components/app/common/AppearanceTabs.vue';
import Heading from '@/components/app/common/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import ProfileLayout from '@/pages/app/profile/partials/Layout.vue';
import { edit } from '@/routes/profile/appearance';
import { type BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Görünüm Ayarları',
        href: edit().url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Görünüm Ayarları" />

        <h1 class="sr-only">Görünüm Ayarları</h1>

        <ProfileLayout>
            <div class="space-y-6">
                <Heading
                    variant="small"
                    title="Görünüm Ayarları"
                    description="Uygulamanın tema ve görünüm tercihlerini buradan güncelleyebilirsiniz."
                />
                <AppearanceTabs />
            </div>
        </ProfileLayout>
    </AppLayout>
</template>