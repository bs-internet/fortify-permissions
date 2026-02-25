<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/app/common/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import UsersLayout from '@/pages/app/users/partials/Layout.vue';
import { index as permissionRoute } from '@/routes/users/permissions';
import { type BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Kullanıcılar', href: '#' },
    { title: 'Yetkiler', href: permissionRoute().url },
];
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Yetkiler" />

        <UsersLayout>
            <div class="space-y-6">
                <div
                    class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
                >
                    <Heading
                        variant="small"
                        title="Yetkiler"
                        description="Sistem kullanıcılarının sahip olduğu yetkiler."
                    />

                    <span>Yetkiler</span>
                </div>
            </div>
        </UsersLayout>
    </AppLayout>
</template>
