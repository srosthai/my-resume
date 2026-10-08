<script setup lang="ts">
defineProps<{
    message?: string;
}>();
</script>

<template>
    <div v-if="message">
        <p class="text-sm text-destructive" role="alert">
            {{ message }}
        </p>
    </div>
</template>
