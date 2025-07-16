<script setup lang="ts">
import { cn } from '@/lib/utils';
import { cva, type VariantProps } from 'class-variance-authority';
import { ref, provide, onMounted, onUnmounted, type Ref } from 'vue';

const dockVariants = cva(
    'mx-auto w-max min-h-[48px] sm:min-h-[52px] lg:min-h-[58px] h-auto p-1.5 sm:p-2 flex gap-1 sm:gap-1.5 lg:gap-2 rounded-xl sm:rounded-xl lg:rounded-2xl border border-border/40 bg-background/80 backdrop-blur-md shadow-lg overflow-visible transition-all duration-300',
    {
        variants: {
            direction: {
                top: 'items-end flex-row',
                middle: 'items-center flex-row',
                bottom: 'items-start flex-row',
            },
        },
        defaultVariants: {
            direction: 'middle',
        },
    },
);

export interface DockProps extends /* @vue-ignore */ VariantProps<typeof dockVariants> {
    class?: string;
    magnification?: number;
    distance?: number;
}

const props = withDefaults(defineProps<DockProps>(), {
    magnification: 35,
    distance: 100,
});

const mouseX = ref<number | null>(null);
const dockRef = ref<HTMLElement>();

// Responsive magnification and distance based on screen size
const responsiveMagnification = ref(props.magnification);
const responsiveDistance = ref(props.distance);

// Update responsive values based on screen size
const updateResponsiveValues = () => {
    const width = window.innerWidth;
    if (width < 640) { // Mobile
        responsiveMagnification.value = 25;
        responsiveDistance.value = 80;
    } else if (width < 1024) { // Tablet
        responsiveMagnification.value = 35;
        responsiveDistance.value = 100;
    } else { // Desktop
        responsiveMagnification.value = 50;
        responsiveDistance.value = 120;
    }
};

// Initialize and listen for resize
onMounted(() => {
    updateResponsiveValues();
    window.addEventListener('resize', updateResponsiveValues);
});

onUnmounted(() => {
    window.removeEventListener('resize', updateResponsiveValues);
});

provide('mouseX', mouseX);
provide('magnification', responsiveMagnification);
provide('distance', responsiveDistance);

const handleMouseMove = (e: MouseEvent) => {
    if (dockRef.value) {
        const rect = dockRef.value.getBoundingClientRect();
        mouseX.value = e.clientX - rect.left;
    }
};

const handleMouseEnter = () => {
    if (dockRef.value) {
        dockRef.value.style.height = 'auto';
    }
};

const handleMouseLeave = () => {
    mouseX.value = null;
};
</script>

<template>
    <div 
        ref="dockRef"
        :class="cn(dockVariants({ direction }), $props.class)"
        @mousemove="handleMouseMove"
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
        style="will-change: transform;"
    >
        <slot />
    </div>
</template>
