<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Heading from '@/components/app/common/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { usePermission } from '@/composables/usePermission';
import { index as countryRoute } from '@/routes/settings/definitions/countries';
import { index as currencyRoute } from '@/routes/settings/definitions/currencies';
import { index as languageRoute } from '@/routes/settings/definitions/languages';
import { index as taxRoute } from '@/routes/settings/definitions/taxes';
import { index as unitRoute } from '@/routes/settings/definitions/units';
import { type NavItem } from '@/types';

const { can } = usePermission();

const sidebarNavItems: NavItem[] = [
    {
        title: 'Birimler',
        href: unitRoute().url,
        show: can('unit.management'),
    },
    {
        title: 'Para Birimleri',
        href: currencyRoute().url,
        show: can('currency.management'),
    },
    {
        title: 'Diller',
        href: languageRoute().url,
        show: can('language.management'),
    },
    {
        title: 'Ülkeler',
        href: countryRoute().url,
        show: can('country.management'),
    },
    {
        title: 'Vergiler',
        href: taxRoute().url,
        show: can('tax.management'),
    },
];

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <div class="px-4 py-6">
        <Heading title="Tanımlamalar"
            description="Sistem içerisinde kullanılan çeşitli tanımlamalara ait değerleri yönetin." />

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
