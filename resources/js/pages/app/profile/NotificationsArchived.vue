<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Archive, ArchiveX, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import Heading from '@/components/app/common/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import AppLayout from '@/layouts/AppLayout.vue';
import ProfileLayout from '@/pages/app/profile/partials/Layout.vue';
import { archived as archivedRoute, index as notificationsIndex } from '@/routes/profile/notifications';
import { type BreadcrumbItem } from '@/types';

type ArchivedNotification = {
    id: string;
    type: string;
    data: {
        title: string;
        message: string;
        updated_at?: string;
    };
    archived_at_human: string;
};

interface Props {
    archivedNotifications: {
        data: ArchivedNotification[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
}

const props = defineProps<Props>();

function handlePageChange(page: number) {
    router.visit(archivedRoute.url(), {
        data: { page },
        preserveScroll: true,
    });
}

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Bildirimler',
        href: notificationsIndex.url(),
    },
    {
        title: 'Arşiv',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Arşivlenmiş Bildirimler" />

        <ProfileLayout>
            <div class="space-y-6">
                <div class="flex items-start justify-between">
                    <Heading variant="small" title="Arşivlenmiş Bildirimler"
                        description="Daha önce arşivlenen bildirimleriniz burada listelenir." />

                    <Button variant="ghost" size="sm" as-child>
                        <Link :href="notificationsIndex.url()">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Bildirimlere Dön
                        </Link>
                    </Button>
                </div>

                <div class="space-y-3">
                    <div v-if="archivedNotifications.data.length === 0"
                        class="flex flex-col items-center justify-center rounded-lg border border-dashed py-12 text-center">
                        <ArchiveX class="mb-4 h-10 w-10 text-muted-foreground" />
                        <p class="text-sm text-muted-foreground">Arşivlenmiş bildirim bulunmuyor.</p>
                    </div>

                    <div v-for="notification in archivedNotifications.data" :key="notification.id"
                        class="flex items-start gap-4 rounded-lg border bg-background p-4 opacity-80 transition-all">
                        <div class="flex gap-3">
                            <div class="mt-1">
                                <Archive class="h-4 w-4 text-muted-foreground" />
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-sm font-medium leading-none">
                                    {{ notification.data.title }}
                                </h4>
                                <p class="text-sm leading-relaxed text-muted-foreground">
                                    {{ notification.data.message }}
                                </p>
                                <p class="text-[10px] uppercase tracking-wider text-muted-foreground">
                                    {{ notification.archived_at_human }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="archivedNotifications.last_page > 1" class="flex justify-center">
                    <Pagination :total="archivedNotifications.total" :items-per-page="archivedNotifications.per_page"
                        :default-page="archivedNotifications.current_page" @update:page="handlePageChange">
                        <PaginationContent>
                            <PaginationPrevious class="cursor-pointer">
                                <ChevronLeft class="h-4 w-4" />
                            </PaginationPrevious>
                            <template v-for="(item, index) in archivedNotifications.links" :key="index">
                                <PaginationItem v-if="item.url && !isNaN(Number(item.label))">
                                    <Button size="icon" variant="ghost" class="h-9 w-9"
                                        :class="{ 'border': item.active }"
                                        @click="handlePageChange(Number(item.label))">
                                        {{ item.label }}
                                    </Button>
                                </PaginationItem>
                            </template>
                            <PaginationNext class="cursor-pointer">
                                <ChevronRight class="h-4 w-4" />
                            </PaginationNext>
                        </PaginationContent>
                    </Pagination>
                </div>
            </div>
        </ProfileLayout>
    </AppLayout>
</template>
