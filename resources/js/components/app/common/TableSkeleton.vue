<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton';

withDefaults(defineProps<{
    rows?: number;
    columns?: number;
}>(), {
    rows: 5,
    columns: 4,
});
</script>

<template>
    <div class="rounded-md border bg-card">
        <div class="border-b px-4 py-3 flex gap-4">
            <Skeleton v-for="col in columns" :key="col" class="h-4 flex-1" />
        </div>
        <div v-for="row in rows" :key="row" class="flex items-center gap-4 border-b last:border-b-0 px-4 py-3">
            <Skeleton v-for="col in columns" :key="col" class="h-4 flex-1" />
        </div>
    </div>
</template>
