<script setup lang="ts">
import { LayoutGrid, Users, Settings2, FileCheckCorner } from 'lucide-vue-next';
import { computed } from 'vue';
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
import { usePermission } from '@/composables/usePermission';
import { dashboard } from '@/routes';
import { index as generalSettings } from '@/routes/settings';
import { index as activities } from '@/routes/settings/activities';
import { index as countries } from '@/routes/settings/definitions/countries';
import { index as currencies } from '@/routes/settings/definitions/currencies';
import { index as languages } from '@/routes/settings/definitions/languages';
import { index as taxes } from '@/routes/settings/definitions/taxes';
import { index as units } from '@/routes/settings/definitions/units';
import { index as usersRoute } from '@/routes/users';
import { index as permissionsRoute } from '@/routes/users/permissions';
import { index as rolesRoute } from '@/routes/users/roles';

const { can } = usePermission();

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
                    show: can('user.management'),
                },
                {
                    title: 'Roller',
                    url: rolesRoute().url,
                    show: can('role.management'),
                },
                {
                    title: 'Yetkiler',
                    url: permissionsRoute().url,
                    show: can('permission.management'),
                },
            ].filter((item) => item.show),
        },
        {
            title: 'Tanımlamalar',
            url: '#',
            icon: FileCheckCorner,
            items: [
                {
                    title: 'Birimler',
                    url: units().url,
                    show: can('unit.management'),
                },
                {
                    title: 'Para Birimi',
                    url: currencies().url,
                    show: can('currency.management'),
                },
                {
                    title: 'Diller',
                    url: languages().url,
                    show: can('language.management'),
                },
                {
                    title: 'Ülke',
                    url: countries().url,
                    show: can('country.management'),
                },
                {
                    title: 'Vergi Oranları',
                    url: taxes().url,
                    show: can('tax.management'),
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
                    show: can('setting.management'),
                },
                {
                    title: 'Etkinlik Kayıtları',
                    url: activities().url,
                    show: can('activity.view'),
                },
            ].filter((item) => item.show),
        },
    ];
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
