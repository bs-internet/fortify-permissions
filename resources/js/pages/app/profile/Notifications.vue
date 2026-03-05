<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { CheckCheck, Bell, Archive, ArchiveRestore, BellOff, ShieldCheck, Info, CheckCircle2, Settings, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed, type Component } from 'vue';
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
import {
    archived,
    markAsRead as markAsReadRoute,
    markAllAsRead as markAllAsReadRoute,
    archive as archiveRoute,
    archiveAllRead as archiveAllReadRoute,
    index as notificationsIndex,
} from '@/routes/profile/notifications';
import { type BreadcrumbItem } from '@/types';

type Notification = {
    id: string;
    type: string;
    data: {
        title: string;
        message: string;
        type?: string;
        updated_at?: string;
    };
    read_at: string | null;
    created_at_human: string;
};

const notificationStyles: Record<string, { icon: Component; color: string; bg: string }> = {
    security: { icon: ShieldCheck, color: 'text-amber-600', bg: 'bg-amber-100 dark:bg-amber-900/30' },
    info: { icon: Info, color: 'text-blue-600', bg: 'bg-blue-100 dark:bg-blue-900/30' },
    success: { icon: CheckCircle2, color: 'text-green-600', bg: 'bg-green-100 dark:bg-green-900/30' },
    settings: { icon: Settings, color: 'text-violet-600', bg: 'bg-violet-100 dark:bg-violet-900/30' },
};

const defaultStyle = { icon: Bell, color: 'text-primary', bg: 'bg-primary/10' };

function getStyle(notification: Notification) {
    const type = notification.data?.type;
    if (type && notificationStyles[type]) {
        return notificationStyles[type];
    }
    return defaultStyle;
}

interface Props {
    notifications: {
        data: Notification[];
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
    unreadCount: number;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Bildirimler',
        href: notificationsIndex().url,
    },
];

function markAsRead(id: string) {
    router.post(markAsReadRoute.url(), {
        notification_id: id,
    }, { preserveScroll: true });
}

function markAllAsRead() {
    router.post(markAllAsReadRoute.url(), {}, {
        preserveScroll: true,
    });
}

function archiveNotification(id: string) {
    router.post(archiveRoute.url(), {
        notification_id: id,
    }, { preserveScroll: true });
}

function archiveAllRead() {
    router.post(archiveAllReadRoute.url(), {}, {
        preserveScroll: true,
    });
}

function handlePageChange(page: number) {
    router.visit(notificationsIndex().url, {
        data: { page },
        preserveScroll: true,
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Bildirimler" />

        <ProfileLayout>
            <div class="space-y-6">
                <div class="flex items-start justify-between">
                    <Heading variant="small" title="Bildirimler"
                        :description="unreadCount > 0 ? `${unreadCount} okunmamış bildiriminiz var.` : 'Tüm bildirimler okundu.'" />

                    <div class="flex gap-2">
                        <Button v-if="unreadCount > 0" variant="outline" size="sm" @click="markAllAsRead">
                            <CheckCheck class="mr-2 h-4 w-4" />
                            Hepsini Oku
                        </Button>

                        <Button v-if="notifications.data.some(n => n.read_at)" variant="outline" size="sm"
                            @click="archiveAllRead">
                            <ArchiveRestore class="mr-2 h-4 w-4" />
                            Okunanları Arşivle
                        </Button>

                        <Button variant="ghost" size="sm" as-child>
                            <Link :href="archived.url()">
                                <Archive class="mr-2 h-4 w-4" />
                                Arşiv
                            </Link>
                        </Button>
                    </div>
                </div>

                <div class="space-y-3">
                    <div v-if="notifications.data.length === 0"
                        class="flex flex-col items-center justify-center rounded-lg border border-dashed py-12 text-center">
                        <BellOff class="mb-4 h-10 w-10 text-muted-foreground" />
                        <p class="text-sm text-muted-foreground">Henüz bir bildiriminiz bulunmuyor.</p>
                    </div>

                    <div v-for="notification in notifications.data" :key="notification.id" :class="[
                        'flex items-start justify-between gap-4 rounded-lg border p-4 transition-all',
                        !notification.read_at ? 'border-primary/20 bg-muted/40 shadow-sm' : 'bg-background opacity-80',
                    ]">
                        <div class="flex gap-3">
                            <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full"
                                :class="!notification.read_at ? getStyle(notification).bg : 'bg-muted'">
                                <component :is="getStyle(notification).icon" class="h-4 w-4"
                                    :class="!notification.read_at ? getStyle(notification).color : 'text-muted-foreground'" />
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-sm font-medium leading-none">
                                    {{ notification.data.title }}
                                </h4>
                                <p class="text-sm leading-relaxed text-muted-foreground">
                                    {{ notification.data.message }}
                                </p>
                                <p class="text-[10px] uppercase tracking-wider text-muted-foreground">
                                    {{ notification.created_at_human }}
                                </p>
                            </div>
                        </div>

                        <div class="flex shrink-0 gap-1">
                            <Button v-if="!notification.read_at" variant="ghost" size="icon"
                                class="h-8 w-8 hover:bg-primary/10 hover:text-primary" title="Okundu olarak işaretle"
                                aria-label="Okundu olarak işaretle" @click="markAsRead(notification.id)">
                                <CheckCheck class="h-4 w-4" />
                            </Button>

                            <Button variant="ghost" size="icon" class="h-8 w-8 hover:bg-muted" title="Arşivle"
                                aria-label="Arşivle" @click="archiveNotification(notification.id)">
                                <Archive class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                </div>

                <div v-if="notifications.last_page > 1" class="flex justify-center">
                    <Pagination :total="notifications.total" :items-per-page="notifications.per_page"
                        :default-page="notifications.current_page" @update:page="handlePageChange">
                        <PaginationContent>
                            <PaginationPrevious class="cursor-pointer">
                                <ChevronLeft class="h-4 w-4" />
                            </PaginationPrevious>
                            <template v-for="(item, index) in notifications.links" :key="index">
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
