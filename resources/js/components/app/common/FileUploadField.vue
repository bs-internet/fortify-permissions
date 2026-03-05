<script setup lang="ts">
import { ImageIcon, Upload, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import InputError from '@/components/app/common/InputError.vue';

const props = withDefaults(defineProps<{
    label: string;
    inputId: string;
    accept?: string;
    currentUrl?: string | null;
    error?: string;
    previewClass?: string;
    containerClass?: string;
}>(), {
    accept: 'image/png,image/jpeg,image/svg+xml',
    currentUrl: null,
    error: '',
    previewClass: 'max-h-20 max-w-full object-contain',
    containerClass: 'bg-muted/30',
});

const emit = defineEmits<{
    change: [file: File];
    delete: [];
}>();

const preview = ref<string | null>(null);

function handleChange(event: Event) {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;

    emit('change', file);

    const reader = new FileReader();
    reader.onload = (e) => {
        preview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
}

function handleDelete() {
    emit('delete');
    preview.value = null;
}

defineExpose({ clearPreview: () => { preview.value = null; } });
</script>

<template>
    <div class="space-y-3">
        <Label>{{ label }}</Label>
        <div :class="['flex h-24 items-center justify-center rounded-md border border-dashed', containerClass]">
            <img v-if="preview" :src="preview" :alt="`${label} Preview`" :class="previewClass" />
            <img v-else-if="currentUrl" :src="currentUrl" :alt="label" :class="previewClass" />
            <ImageIcon v-else class="h-8 w-8 text-muted-foreground/30" />
        </div>
        <div class="flex gap-2">
            <Label :for="inputId"
                class="flex h-8 cursor-pointer items-center gap-1.5 rounded-md border px-3 text-xs font-medium hover:bg-accent">
                <Upload class="h-3.5 w-3.5" />
                Yükle
            </Label>
            <input :id="inputId" type="file" :accept="accept" class="hidden" @change="handleChange" />
            <Button v-if="currentUrl" variant="ghost" size="sm" type="button" class="h-8 px-2" @click="handleDelete">
                <Trash2 class="h-3.5 w-3.5" />
            </Button>
        </div>
        <InputError :message="error" />
    </div>
</template>
