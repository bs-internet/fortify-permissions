<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { CheckCheck, Bell, Archive, ArchiveRestore, BellOff } from 'lucide-vue-next';
import Heading from '@/components/app/common/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import ProfileLayout from '@/pages/app/profile/partials/Layout.vue';
import {
    archived,
    markAsRead as markAsReadRoute,
    markAllAsRead as markAllAsReadRoute,
    archive as archiveRoute,
    archiveAllRead as archiveAllReadRoute,
} from '@/routes/profile/notifications';
import { type BreadcrumbItem } from '@/types';

type Notification = {
    id: string;
    type: string;
    data: {
        title: string;
        message: string;
        updated_at?: string;
    };
    read_at: string | null;
    created_at_human: string;
};

interface Props {
    notifications: {
        data: Notification[];
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
        href: '/profile/notifications',
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
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Bildirimler" />

        <ProfileLayout>
            <div class="space-y-6">
                <div class="flex items-start justify-between">
                    <Heading
                        variant="small"
                        title="Bildirimler"
                        :description="unreadCount > 0 ? `${unreadCount} okunmamış bildiriminiz var.` : 'Tüm bildirimler okundu.'"
                    />

                    <div class="flex gap-2">
                        <Button
                            v-if="unreadCount > 0"
                            variant="outline"
                            size="sm"
                            @click="markAllAsRead"
                        >
                            <CheckCheck class="mr-2 h-4 w-4" />
                            Hepsini Oku
                        </Button>

                        <Button
                            v-if="notifications.data.some(n => n.read_at)"
                            variant="outline"
                            size="sm"
                            @click="archiveAllRead"
                        >
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
                    <div
                        v-if="notifications.data.length === 0"
                        class="flex flex-col items-center justify-center rounded-lg border border-dashed py-12 text-center"
                    >
                        <BellOff class="mb-4 h-10 w-10 text-muted-foreground" />
                        <p class="text-sm text-muted-foreground">Henüz bir bildiriminiz bulunmuyor.</p>
                    </div>

                    <div
                        v-for="notification in notifications.data"
                        :key="notification.id"
                        :class="[
                            'flex items-start justify-between gap-4 rounded-lg border p-4 transition-all',
                            !notification.read_at ? 'border-primary/20 bg-muted/40 shadow-sm' : 'bg-background opacity-80',
                        ]"
                    >
                        <div class="flex gap-3">
                            <div class="mt-1">
                                <Bell :class="['h-4 w-4', !notification.read_at ? 'text-primary' : 'text-muted-foreground']" />
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
                            <Button
                                v-if="!notification.read_at"
                                variant="ghost"
                                size="icon"
                                class="h-8 w-8 hover:bg-primary/10 hover:text-primary"
                                title="Okundu olarak işaretle"
                                @click="markAsRead(notification.id)"
                            >
                                <CheckCheck class="h-4 w-4" />
                            </Button>

                            <Button
                                variant="ghost"
                                size="icon"
                                class="h-8 w-8 hover:bg-muted"
                                title="Arşivle"
                                @click="archiveNotification(notification.id)"
                            >
                                <Archive class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                </div>

                <nav v-if="notifications.links.length > 3" class="flex justify-center gap-1">
                    <button
                        v-for="(link, k) in notifications.links"
                        :key="k"
                        v-html="link.label"
                        :disabled="!link.url || link.active"
                        @click="router.visit(link.url!)"
                        class="flex h-8 min-w-[32px] items-center justify-center rounded-md border px-2 text-xs transition-colors"
                        :class="[
                            link.active ? 'border-primary bg-primary text-primary-foreground' : 'hover:bg-accent',
                            !link.url ? 'cursor-not-allowed opacity-50' : 'cursor-pointer',
                        ]"
                    />
                </nav>
            </div>
        </ProfileLayout>
    </AppLayout>
</template>
