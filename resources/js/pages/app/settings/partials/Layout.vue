<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Heading from '@/components/app/common/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { usePermission } from '@/composables/usePermission';
import { index as settingsIndex } from '@/routes/settings';
import { index as activityIndex } from '@/routes/settings/activities';
import { type NavItem } from '@/types';

const { can } = usePermission();


const sidebarNavItems: NavItem[] = [
    {
        title: 'Genel Ayarlar',
        href: settingsIndex(),
        show: can('setting.management'),
    },
    {
        title: 'Etkinlik Kayıtları',
        href: activityIndex(),
        show: can('activity.view'),
    },
];

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <div class="px-4 py-6">
        <Heading title="Ayarlar" description="Sistem tercihlerinizi ve hesap geçmişinizi yönetin." />

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1">
                    <Button v-for="item in sidebarNavItems.filter(i => i.show)" :key="item.title" variant="ghost"
                        :class="[
                            'w-full justify-start',
                            { 'bg-muted': isCurrentUrl(item.href) },
                        ]" as-child>
                        <Link :href="item.href">
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 lg:hidden" />

            <div class="flex-1 md:max-w-4xl">
                <section class="space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
