<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';

interface Props {
    open: boolean;
    title?: string;
    description?: string;
    confirmLabel?: string;
    cancelLabel?: string;
    processing?: boolean;
    icon?: string;
    tone?: 'danger' | 'default';
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Are you sure?',
    description: 'This action cannot be undone.',
    confirmLabel: 'Confirm',
    cancelLabel: 'Cancel',
    processing: false,
    icon: 'triangleAlert',
    tone: 'danger',
});

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'confirm'): void;
    (e: 'cancel'): void;
}>();

const onOpenChange = (value: boolean) => {
    emit('update:open', value);
    if (!value) emit('cancel');
};
</script>

<template>
    <Dialog :open="props.open" @update:open="onOpenChange">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <div
                    class="mb-1 flex size-12 items-center justify-center rounded-full"
                    :class="props.tone === 'danger' ? 'bg-destructive/10 text-destructive' : 'bg-primary/10 text-primary'"
                >
                    <Icon :name="props.icon" class="size-6" />
                </div>
                <DialogTitle class="text-lg font-semibold tracking-tight">{{ props.title }}</DialogTitle>
                <DialogDescription class="text-sm leading-relaxed text-muted-foreground">
                    {{ props.description }}
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="mt-2 gap-2 sm:gap-2">
                <Button variant="outline" :disabled="props.processing" @click="onOpenChange(false)">
                    {{ props.cancelLabel }}
                </Button>
                <Button
                    :variant="props.tone === 'danger' ? 'destructive' : 'default'"
                    :disabled="props.processing"
                    @click="emit('confirm')"
                >
                    <Icon v-if="props.processing" name="loaderCircle" class="size-4 animate-spin" />
                    {{ props.confirmLabel }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
