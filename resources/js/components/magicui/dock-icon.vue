<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed, inject, ref, type Ref } from 'vue';

interface DockIconProps {
    class?: string;
    size?: number;
}

const props = withDefaults(defineProps<DockIconProps>(), {
    size: 36,
});

const mouseX = inject<Ref<number | null>>('mouseX', ref(null));
const magnification = inject('magnification', ref(35));
const distance = inject('distance', ref(100));

const iconRef = ref<HTMLElement>();

const iconScale = computed(() => {
    if (mouseX.value === null || !iconRef.value) {
        return 1;
    }

    const rect = iconRef.value.getBoundingClientRect();
    const dockRect = iconRef.value.closest('[data-dock]')?.getBoundingClientRect();
    
    if (!dockRect) return 1;
    
    const iconCenterX = rect.left + rect.width / 2 - dockRect.left;
    const distanceFromMouse = Math.abs(mouseX.value - iconCenterX);
    
    // Get current responsive values
    const currentMagnification = typeof magnification === 'object' ? magnification.value : magnification;
    const currentDistance = typeof distance === 'object' ? distance.value : distance;
    
    // Smoother scaling curve using exponential falloff
    const normalizedDistance = Math.min(distanceFromMouse / currentDistance, 1);
    const easedDistance = 1 - Math.pow(normalizedDistance, 1.5);
    const scale = 1 + (currentMagnification / 100) * Math.max(0, easedDistance);
    
    return Math.min(scale, 1 + currentMagnification / 100);
});

const iconSize = computed(() => {
    return props.size;
});
</script>

<template>
    <div 
        ref="iconRef"
        :class="cn(
            'group flex cursor-pointer items-center justify-center rounded-full transition-all duration-300 ease-out shrink-0',
            'w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12',
            $props.class
        )"
        :style="{
            transform: `scale(${iconScale})`,
            transformOrigin: 'center bottom',
            willChange: 'transform',
        }"
    >
        <div class="flex h-full w-full items-center justify-center">
            <slot />
        </div>
    </div>
</template>
