<script setup lang="ts">
import {
    LayoutGrid,
    Settings2,
} from 'lucide-vue-next';
import AppLogo from '@/components/app/common/AppLogo.vue';
import NavMain from '@/components/app/NavMain.vue';
import NavUser from '@/components/app/NavUser.vue';
import type { SidebarProps } from '@/components/ui/sidebar';

import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarRail,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';

const props = withDefaults(defineProps<SidebarProps>(), {
    collapsible: 'icon',
});

// This is sample data.
const data = {
    navMain: [
        {
            title: 'Başlangıç',
            url: dashboard().url,
            icon: LayoutGrid,
        },
        {
            title: 'Ayarlar',
            url: '#',
            icon: Settings2,
            items: [
                {
                    title: 'General',
                    url: '#',
                },
                {
                    title: 'Team',
                    url: '#',
                },
                {
                    title: 'Billing',
                    url: '#',
                },
                {
                    title: 'Limits',
                    url: '#',
                },
            ],
        },
    ],
};
</script>

<template>
    <Sidebar v-bind="props">
        <SidebarHeader>
            <div class="flex items-center gap-2 px-2 py-1.5">
                <AppLogo />
            </div>
        </SidebarHeader>
        <SidebarContent>
            <NavMain :items="data.navMain" />
        </SidebarContent>
        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
        <SidebarRail />
    </Sidebar>
</template>
