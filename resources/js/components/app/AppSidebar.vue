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
import { index as units } from '@/routes/settings/definitions/units';
import { index as usersRoute } from '@/routes/users';
import { index as permissionsRoute } from '@/routes/users/permissions';
import { index as rolesRoute } from '@/routes/users/roles';

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

import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const permissions = computed<string[]>(() => {
    return (page.props.auth as any)?.permissions || [];
});

const hasPermission = (permission: string) => {
    return permissions.value.includes(permission);
};

const moduleNav = computed(() => {
    const nav = [
        {
            title: 'Kullanıcılar',
            url: '#',
            icon: Users,
            items: [
                {
                    title: 'Kullanıcılar',
                    url: usersRoute().url,
                    show: hasPermission('user.management'),
                },
                {
                    title: 'Roller',
                    url: rolesRoute().url,
                    show: hasPermission('role.management'),
                },
                {
                    title: 'Yetkiler',
                    url: permissionsRoute().url,
                    show: hasPermission('permission.management'),
                },
            ].filter((item) => item.show),
        },
        {
            title: 'Ayarlar',
            url: '#',
            icon: Settings2,
            items: [
                {
                    title: 'Genel Ayarlar',
                    url: generalSettings().url,
                    show: hasPermission('setting.management'),
                },
                {
                    title: 'Tanımlamalar',
                    url: units().url, // Note: Units represents Definitions broadly in this menu route
                    show: hasPermission('definition.management'),
                },
                {
                    title: 'Etkinlik Kayıtları',
                    url: activities().url,
                    show: hasPermission('activity.view'),
                },
            ].filter((item) => item.show),
        },
    ];

    // Sadece içi dolu (alt elemanı olan) parent menüleri göster
    return nav.filter((menu) => menu.items.length > 0);
});
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
            <NavMain label="Platform" :items="moduleNav" />
        </SidebarContent>
        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
        <SidebarRail />
    </Sidebar>
</template>
