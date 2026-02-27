<script setup lang="ts">
import { Empty, EmptyContent, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { Button } from '@/components/ui/button';
import { FileQuestion } from 'lucide-vue-next';

type Props = {
    title: string;
    description: string;
    icon?: any;
    actionLabel?: string;
};

withDefaults(defineProps<Props>(), {
    icon: FileQuestion,
});

const emit = defineEmits(['action']);
</script>

<template>
    <Empty class="w-full flex-col justify-center items-center py-12">
        <EmptyHeader v-if="icon">
            <EmptyMedia variant="icon" class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-muted">
                <component :is="icon" class="h-8 w-8 text-muted-foreground" />
            </EmptyMedia>
        </EmptyHeader>
        <EmptyContent class="text-center">
            <EmptyTitle class="mt-4 text-xl font-semibold">{{ title }}</EmptyTitle>
            <EmptyDescription class="mt-2 text-muted-foreground">{{ description }}</EmptyDescription>
            <Button v-if="actionLabel" @click="emit('action')" class="mt-6">
                {{ actionLabel }}
            </Button>
        </EmptyContent>
    </Empty>
</template>
