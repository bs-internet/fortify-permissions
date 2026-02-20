<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Archive, ArchiveX } from 'lucide-vue-next';
import Heading from '@/components/app/common/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import ProfileLayout from '@/pages/app/profile/partials/Layout.vue';
import { index as notificationsIndex } from '@/routes/profile/notifications';
import { type BreadcrumbItem } from '@/types';

type ArchivedNotification = {
    id: string;
    type: string;
    data: {
        title: string;
        message: string;
        updated_at?: string;
    };
    archived_at: string;
};

interface Props {
    archivedNotifications: {
        data: ArchivedNotification[];
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
}

defineProps<Props>();

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
                    <Heading
                        variant="small"
                        title="Arşivlenmiş Bildirimler"
                        description="Daha önce arşivlenen bildirimleriniz burada listelenir."
                    />

                    <Button variant="ghost" size="sm" as-child>
                        <Link :href="notificationsIndex.url()">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Bildirimlere Dön
                        </Link>
                    </Button>
                </div>

                <div class="space-y-3">
                    <div
                        v-if="archivedNotifications.data.length === 0"
                        class="flex flex-col items-center justify-center rounded-lg border border-dashed py-12 text-center"
                    >
                        <ArchiveX class="mb-4 h-10 w-10 text-muted-foreground" />
                        <p class="text-sm text-muted-foreground">Arşivlenmiş bildirim bulunmuyor.</p>
                    </div>

                    <div
                        v-for="notification in archivedNotifications.data"
                        :key="notification.id"
                        class="flex items-start gap-4 rounded-lg border bg-background p-4 opacity-80 transition-all"
                    >
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
                                    {{ notification.archived_at }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <nav v-if="archivedNotifications.links.length > 3" class="flex justify-center gap-1">
                    <button
                        v-for="(link, k) in archivedNotifications.links"
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
