<script setup>
import Icon from '@/components/Icon.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    note: {
        type: Object,
        required: true,
    },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Notes', href: '/notes' },
    { title: props.note.title, href: '' },
];

const copiedCommands = ref(new Set());

const copyCommand = async (command, stepIndex, commandIndex) => {
    try {
        await navigator.clipboard.writeText(command);
        const key = `${stepIndex}-${commandIndex}`;
        copiedCommands.value.add(key);
        setTimeout(() => {
            copiedCommands.value.delete(key);
        }, 2000);
    } catch (err) {
        console.error('Failed to copy command:', err);
    }
};

const isCopied = (stepIndex, commandIndex) => {
    return copiedCommands.value.has(`${stepIndex}-${commandIndex}`);
};

const getStatusColor = (status) => {
    const colors = {
        draft: 'bg-gray-500/10 text-gray-500 border-gray-500/20',
        published: 'bg-green-500/10 text-green-500 border-green-500/20',
        archived: 'bg-red-500/10 text-red-500 border-red-500/20',
    };
    return colors[status] || 'bg-gray-500/10 text-gray-500 border-gray-500/20';
};
</script>

<template>
    <Head :title="note.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="route('notes.index')">
                        <Button variant="outline" size="icon" class="rounded-xl">
                            <Icon name="arrowLeft" class="size-4" />
                        </Button>
                    </Link>
                    <div class="flex items-center gap-4">
                        <div class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary/70 text-primary-foreground shadow-sm">
                            <Icon name="stickyNote" class="size-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold tracking-tight">{{ note.title }}</h1>
                            <p class="text-sm text-muted-foreground">Note details</p>
                        </div>
                    </div>
                </div>
                <Link :href="route('notes.edit', note.id)">
                    <Button class="rounded-xl shadow-sm">
                        <Icon name="squarePen" class="size-4" />
                        Edit Note
                    </Button>
                </Link>
            </div>

            <!-- Note Info -->
            <div class="rounded-2xl border bg-card shadow-sm">
                <div class="space-y-4 p-6 sm:p-8">
                    <div class="flex flex-wrap items-center gap-2">
                        <Badge variant="secondary" class="rounded-full font-normal">{{ note.category }}</Badge>
                        <Badge variant="outline" :class="getStatusColor(note.status)" class="rounded-full font-normal capitalize">
                            {{ note.status }}
                        </Badge>
                        <Badge
                            v-if="note.is_featured"
                            variant="outline"
                            class="rounded-full border-yellow-500/20 bg-yellow-500/10 font-normal text-yellow-500"
                        >
                            <Icon name="star" class="mr-1 size-3 fill-current" />
                            Featured
                        </Badge>
                    </div>

                    <div class="space-y-1">
                        <h2 class="text-xl font-semibold tracking-tight">{{ note.title }}</h2>
                        <p class="text-muted-foreground">{{ note.description }}</p>
                    </div>

                    <!-- Tags -->
                    <div v-if="note.tags && note.tags.length" class="space-y-2">
                        <h4 class="text-xs font-medium uppercase tracking-wider text-muted-foreground">Tags</h4>
                        <div class="flex flex-wrap gap-2">
                            <Badge v-for="tag in note.tags" :key="tag" variant="secondary" class="rounded-full font-normal">
                                {{ tag }}
                            </Badge>
                        </div>
                    </div>

                    <!-- Meta Information -->
                    <div class="grid grid-cols-1 gap-4 border-t pt-4 text-sm text-muted-foreground md:grid-cols-3">
                        <div class="flex items-center gap-2">
                            <Icon name="user" class="size-4" />
                            <span>{{ note.user?.name || 'Admin' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Icon name="calendar" class="size-4" />
                            <span>Created: {{ new Date(note.created_at).toLocaleDateString() }}</span>
                        </div>
                        <div v-if="note.published_at" class="flex items-center gap-2">
                            <Icon name="calendar" class="size-4" />
                            <span>Published: {{ new Date(note.published_at).toLocaleDateString() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overview -->
            <div class="rounded-2xl border bg-card shadow-sm">
                <div class="space-y-3 p-6 sm:p-8">
                    <h2 class="flex items-center gap-2 text-base font-semibold tracking-tight">
                        <Icon name="lightbulb" class="size-5 text-muted-foreground" />
                        Overview
                    </h2>
                    <p class="leading-relaxed text-muted-foreground">
                        {{ note.content?.overview }}
                    </p>
                </div>
            </div>

            <!-- Requirements -->
            <div v-if="note.content?.requirements?.length" class="rounded-2xl border bg-card shadow-sm">
                <div class="space-y-3 p-6 sm:p-8">
                    <h2 class="flex items-center gap-2 text-base font-semibold tracking-tight">
                        <Icon name="book" class="size-5 text-muted-foreground" />
                        Requirements
                    </h2>
                    <ul class="space-y-2">
                        <li
                            v-for="requirement in note.content.requirements"
                            :key="requirement"
                            class="flex items-center gap-2 text-muted-foreground"
                        >
                            <div class="size-2 flex-shrink-0 rounded-full bg-primary"></div>
                            {{ requirement }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Steps -->
            <div v-if="note.content?.steps?.length" class="space-y-4">
                <h2 class="flex items-center gap-2 text-lg font-semibold tracking-tight">
                    <Icon name="terminal" class="size-5 text-muted-foreground" />
                    Installation Steps
                </h2>

                <div
                    v-for="(step, stepIndex) in note.content.steps"
                    :key="stepIndex"
                    class="rounded-2xl border bg-card shadow-sm"
                >
                    <div class="space-y-4 p-6 sm:p-8">
                        <div class="flex items-start gap-3">
                            <span class="inline-flex size-8 flex-shrink-0 items-center justify-center rounded-full bg-primary text-sm font-semibold text-primary-foreground">
                                {{ stepIndex + 1 }}
                            </span>
                            <div class="space-y-1">
                                <h3 class="text-base font-semibold tracking-tight">{{ step.title }}</h3>
                                <p class="text-sm text-muted-foreground">{{ step.description }}</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div
                                v-for="(command, commandIndex) in step.commands"
                                :key="commandIndex"
                                class="group relative"
                            >
                                <div class="rounded-xl border border-border/50 bg-muted/50 p-3 pr-12 font-mono text-sm transition-all duration-300 hover:border-primary/50">
                                    <code class="break-all text-foreground">{{ command }}</code>
                                </div>
                                <Button
                                    size="sm"
                                    variant="ghost"
                                    class="absolute right-2 top-2 size-8 rounded-lg p-0 opacity-0 transition-opacity hover:bg-primary/10 group-hover:opacity-100"
                                    @click="copyCommand(command, stepIndex, commandIndex)"
                                >
                                    <Icon v-if="isCopied(stepIndex, commandIndex)" name="check" class="size-4 text-green-500" />
                                    <Icon v-else name="copy" class="size-4" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
code {
    font-family: 'Fira Code', 'Consolas', 'Monaco', monospace;
}
</style>
