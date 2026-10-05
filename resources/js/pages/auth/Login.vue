<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import SiteBackdrop from '@/components/SiteBackdrop.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Eye, EyeOff, LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister?: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="login-screen relative flex min-h-svh items-center justify-center px-4 py-12 sm:px-6">
        <SiteBackdrop />
        <Head title="Sign in" />

        <div class="relative z-10 w-full max-w-[26rem]">
            <Link :href="route('home')" class="login-return">
                <ArrowLeft class="size-3.5" />
                Back to the site
            </Link>

            <div class="login-sheet bg-card px-6 py-8 sm:px-8 sm:py-10">
                <h1 class="login-title">Sign in</h1>

                <p v-if="status" class="login-status" role="status">{{ status }}</p>

                <form class="mt-8 flex flex-col gap-5" @submit.prevent="submit">
                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            :aria-invalid="form.errors.email ? true : undefined"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <div class="flex items-baseline justify-between gap-3">
                            <Label for="password">Password</Label>
                            <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm" :tabindex="5">
                                Forgot password?
                            </TextLink>
                        </div>
                        <div class="relative">
                            <Input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                :tabindex="2"
                                autocomplete="current-password"
                                class="pr-11"
                                :aria-invalid="form.errors.password ? true : undefined"
                            />
                            <button
                                type="button"
                                class="login-reveal"
                                :aria-pressed="showPassword"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                @click="showPassword = !showPassword"
                            >
                                <EyeOff v-if="showPassword" class="size-4" />
                                <Eye v-else class="size-4" />
                            </button>
                        </div>
                        <InputError :message="form.errors.password" />
                    </div>

                    <Label for="remember" class="flex w-fit items-center gap-2.5 text-sm">
                        <Checkbox id="remember" v-model="form.remember" :tabindex="3" />
                        <span>Remember me</span>
                    </Label>

                    <button type="submit" class="login-submit btn-3d" :tabindex="4" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="size-4 animate-spin" />
                        {{ form.processing ? 'Signing in' : 'Sign in' }}
                    </button>
                </form>

                <p v-if="canRegister" class="mt-6 text-sm text-muted-foreground">
                    Need an account?
                    <TextLink :href="route('register')" :tabindex="6">Sign up</TextLink>
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.login-return {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    margin-bottom: 1rem;
    font-family: 'JetBrains Mono', ui-monospace, 'SFMono-Regular', Menlo, monospace;
    font-size: 0.72rem;
    letter-spacing: 0.04em;
    color: var(--muted-foreground);
    transition: color 0.2s ease;
}

.login-return:hover {
    color: var(--foreground);
}

.login-return:focus-visible {
    outline: 2px solid var(--accent-ink);
    outline-offset: 3px;
    border-radius: 4px;
}

.login-sheet {
    border: 1.5px solid color-mix(in oklab, var(--card-ink) 78%, transparent);
    border-radius: 16px;
    box-shadow:
        var(--card-lift) var(--card-lift) 0 0 var(--card-ink),
        0 18px 36px -28px color-mix(in oklab, var(--card-ink) 72%, transparent);
    animation: login-land 880ms cubic-bezier(0.22, 1, 0.36, 1) both;
}

.login-title {
    font-family: 'Instrument Serif', 'Iowan Old Style', Georgia, serif;
    font-size: clamp(2.6rem, 8vw, 3.25rem);
    font-weight: 400;
    line-height: 0.92;
    letter-spacing: -0.03em;
    color: var(--foreground);
}

.login-status {
    margin-top: 1rem;
    font-size: 0.925rem;
    line-height: 1.45;
    color: var(--foreground);
}

.login-sheet :deep(input) {
    height: 2.75rem;
    border-radius: 12px;
    background: var(--background);
    caret-color: var(--accent-ink);
    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;
}

.login-sheet :deep(input::selection) {
    background: color-mix(in oklab, var(--accent-ink) 28%, transparent);
}

.login-sheet :deep(input:focus) {
    border-color: var(--accent-ink);
    box-shadow: 0 0 0 3px color-mix(in oklab, var(--accent-ink) 24%, transparent);
    outline: none;
}

.login-reveal {
    position: absolute;
    top: 50%;
    right: 0.35rem;
    display: inline-flex;
    width: 2rem;
    height: 2rem;
    align-items: center;
    justify-content: center;
    translate: 0 -50%;
    border-radius: 8px;
    color: var(--muted-foreground);
}

.login-reveal:hover {
    color: var(--foreground);
}

.login-reveal:focus-visible {
    outline: 2px solid var(--accent-ink);
    outline-offset: 2px;
}

.login-submit {
    display: inline-flex;
    height: 2.85rem;
    width: 100%;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 0.25rem;
    border-radius: 12px;
    background: var(--foreground);
    color: var(--background);
    font-size: 0.95rem;
    font-weight: 500;
}

.login-submit:disabled {
    opacity: 0.7;
}

.login-submit:focus-visible {
    outline: 2px solid var(--accent-ink);
    outline-offset: 3px;
}

@keyframes login-land {
    from {
        opacity: 0;
        filter: blur(10px);
        translate: 12px 22px;
    }
    to {
        opacity: 1;
        filter: blur(0);
        translate: 0 0;
    }
}

@media (prefers-reduced-motion: reduce) {
    .login-sheet {
        animation: none;
    }
}
</style>
