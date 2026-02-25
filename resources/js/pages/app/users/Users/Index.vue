<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/app/common/Heading.vue';
import UsersLayout from '@/pages/app/users/partials/Layout.vue';
import { type BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Kullanıcılar', href: '#' },
    { title: 'Kayıtlı Kullanıcılar', href: '#' },
];
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Kayıtlı Kullanıcılar" />

        <UsersLayout>
            <div class="space-y-6">
                <div
                    class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
                >
                    <Heading
                        variant="small"
                        title="Kayıtlı Kullanıcılar"
                        description="Sistem üzerinde kayıtlı kullanıcılar."
                    />

                    <span>Kullanıcılar</span>
                </div>
            </div>
        </UsersLayout>
    </AppLayout>
</template>
