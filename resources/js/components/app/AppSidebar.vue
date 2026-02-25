<script setup lang="ts">
import { LayoutGrid, Users, Settings2 } from 'lucide-vue-next';
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
import { index as generalSettings } from '@/routes/settings';
import { index as activities } from '@/routes/settings/activities';

const props = withDefaults(defineProps<SidebarProps>(), {
    collapsible: 'icon',
});

const mainNav = [
    {
        title: 'Başlangıç',
        url: dashboard().url,
        icon: LayoutGrid,
    },
];

const data = {
    moduleNav: [
        {
            title: 'Kullanıcılar',
            url: '#',
            icon: Users,
            items: [
                {
                    title: 'Kullanıcılar',
                    url: '#',
                },
                {
                    title: 'Birimler',
                    url: '#',
                },
                {
                    title: 'Yetkiler',
                    url: '#',
                },
            ],
        },
        {
            title: 'Ayarlar',
            url: '#',
            icon: Settings2,
            items: [
                {
                    title: 'Genel Ayarlar',
                    url: generalSettings().url,
                },
                {
                    title: 'Tanımlamalar',
                    url: activities().url,
                },
                {
                    title: 'Etkinlik Kayıtları',
                    url: activities().url,
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
            <NavMain :items="mainNav" />
            <NavMain label="Platform" :items="data.moduleNav" />
        </SidebarContent>
        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
        <SidebarRail />
    </Sidebar>
</template>
